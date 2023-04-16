<?php
namespace Drupal\jag\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\Markup;
use Drupal\Core\Datetime\DrupalDateTime;
use DateTime;

/**
 * Provides route responses for the Example module.
 */
class EvenementenController extends ControllerBase {
 
  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function mainPage($event) {    
    $forms = \Drupal::formBuilder()->getForm('Drupal\jag\Form\EvenementenBeheerForm');
    
    $html ="<h1>JAG Evenementenbeheer</h1>";                    
    
    if ($event == 0){
      $data['form'] = [
        'form' => $forms,
      ];   

      $ret = $this->get_data($header,$rows);
        
      $data['evenementen'] = [
        '#theme' => 'table',
        '#header' => $header,
        '#rows' => $rows,
      ];
    } else {
      $event_id = $this->get_event($event,$header,$rows);      
      $data['evenementen'] = [
        '#theme' => 'table',
        '#header' => $header,
        '#rows' => $rows,
      ];

      $ret = $this->get_organisator($event_id,$header_org,$rows_org);      
      $data['organisator'] = [
        '#theme' => 'table',
        '#header' => $header_org,
        '#rows' => $rows_org,
      ];
    
      $ret = $this->get_attendees($event,$header_att,$rows_att);      
      $data['attendees'] = [
        '#theme' => 'table',
        '#header' => $header_att,
        '#rows' => $rows_att,
      ];
      $formresponse = \Drupal::formBuilder()->getForm('Drupal\jag\Form\EventResponseForm');   
      $data['response'] = [
        'form' => $formresponse
      ]; 
    }
    //\Drupal\Core\Database\Database::setActiveConnection();      
    return $data;

     
  }
  public function get_data(&$header,&$rows){
    $dteStart = null;
    $dteEnd = null;
    $dtestart = null;
    $id = $this->get_ID($roles,$lidtype,$group_id);    

    if(isset($_SESSION['DateFrom'])){
        $dteStart = $_SESSION['DateFrom'];    
        $dte_start = \date_create($dteStart);
        $dte_start2 = $dte_start->format('Y-m-d');
    }
    if(isset($_SESSION['DateUntil'])){
      $dteEnd = $_SESSION['DateUntil'];
      $dte_end = \date_create($dteEnd);
      $dte_end2 = $dte_start->format('Y-m-d');
    }
           
    $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");    
    $query = $database->select('mod_club_event_date' , 'u');        
    $query->join('mod_club_event','u1', 'u.event_id = u1.id');
    $query->join('mod_club_event_agegroup','u2','u2.event_id = u.event_id');
    $query->join('mod_club_agegroup','u3','u3.id = u2.agegroup_id');
    $query->fields('u',['id','event_id', 'start', 'end', 'attend_till' , 'cancelled_on']);
    $query->fields('u1',['name_nl']);
    $query->fields('u2',['agegroup_id']); 
    $query->fields('u3',['name_nl']);
    
    if (isset($dte_start)){
      $dte_start2 = $dte_start->format('Y-m-d');
    }
    if (isset($dte_end)){
      $dte_end2 = $dte_start->format('Y-m-d');
    }

    if (isset($dte_Start2)){
      $dtestart = $dte_start2;
    }
    #$dtestart  = '2023-01-01'; // ? year problem ?
    $query->condition('u.start',$dteStart,'>');      

    //$query->addExpression('start , u3_name_nl','s'); 
    
    $query->orderBy('start','ASC');  //old selection parameter
    $query->orderBy('event_id');
    $query->orderBy('u3_name_nl');
    //$query->orderBy('s','ASC');  // new selection parameter - does not work
            
    $results = $query->execute();
    $rows = array();
    $header = array('Omschrijving','Start','Geannuleerd op','leeftijdsgroep');
    $id = ' ';
    foreach ($results as $row => $content){
      if ($content->id != $id){
        if ($lidtype != '1') {
          $url_string = $content->name_nl;
        }else{
          //$url_base = '/jag/evenementen/'.$content->id; //version pre 202212
          $url_base = '/jag/evenement/'.$content->id; //version pre 202212
          $url_string = '<a href="'.$url_base.'">'.$content->name_nl.'</a>';
        }
        $str_start = substr($content->start,0,16);
        $rows[] = array('data' => array(Markup::create($url_string),                                      
                                      $str_start,                                      
                                      $content->cancelled_on,                                                                            
                                      $content->u3_name_nl,
                                    ));
        $id = $content->id;
      } else {        
        $rows[count($rows) -1]['data'][3] = $rows[count($rows) -1]['data'][3].$content->u3_name_nl;
      }

    } 
    \Drupal\Core\Database\Database::setActiveConnection();             
    return 0;
  }
  public function get_event($event,&$header,&$rows){
    $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");    
    $query = $database->select('mod_club_event_date' , 'u');    
    $query->join('mod_club_event','u1', 'u.event_id = u1.id');
    $query->fields('u',[id,event_id, start, end, attend_till , cancelled_on]);
    $query->fields('u1',[name_nl,address,zipcode,city,description_nl,creator]);    
    $query->condition('u.id',$event,'=');      
    
    $results = $query->execute()->fetchAssoc();
    if ($results != false){
      $rows = array();
      $header = array('Omschrijving','Waarde');

      $rows = array();                
      $rows[] = array('data' => array('Titel', $results['name_nl']));
      $rows[] = array('data' => array('Start',$results['start']));
      $rows[] = array('data' => array('Einde',$results['end']));
      $rows[] = array('data' => array('Adres',$results['address']));
      $rows[] = array('data' => array('Postcode',$results['zipcode']));
      $rows[] = array('data' => array('Stad',$results['city']));
      //$rows[] = array('data' => array('Omschrijving',$results[description_nl]));
      $rows[] = array('data' => array('Omschrijving',Markup::create($results[description_nl])));

      $data['waarden'] = [
        '#theme' => 'table',          
        '#rows' => $rows,
      ];
    
      \Drupal\Core\Database\Database::setActiveConnection();             
    
     return $results[creator];
    } else {
      return -1;
    }
    
  }

  public function get_organisator($event,&$header,&$rows){
    $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");    
    $query = $database->select('mod_club_user' , 'u');        
    $query->fields('u',['username', 'firstname', 'lastname']);
    
    $query->condition('u.id',$event,'=');      
    
    $results = $query->execute()->fetchAssoc();
    $rows = array();
    $header = array('Organisator');
    $naam = $results[firstname].' '.$results[lastname];
    $rows = array();                
    $urlname = '/jag/lid/'.$results[username];    
    $url = '<a href="'.$urlname.'">'.$naam.'<a>';

    $rows[] = array('data' => array(Markup::create($url)));

    $data['organisator'] = [
      '#theme' => 'table',          
      '#rows' => $rows,
    ];
    

    \Drupal\Core\Database\Database::setActiveConnection();             
       
    return data;
    
  }

  public function get_organisator2($event,&$header,&$rows){
    $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");    
    $query = $database->select('mod_club_event_organisator' , 'u');    
    //$query->join('mod_club_event','u1', 'u.event_id = u1.id');
    $query->fields('u',[event_id, user_id]);
    //$query->fields('u1',[name_nl,address,zipcode,city,description_nl]);    
    $query->condition('u.event_id',$event,'=');      
    
    $results = $query->execute()->fetchAssoc();
    $rows = array();
    $header = array('Omschrijving','Waarde');

    $rows = array();                
    $rows[] = array('data' => array('User', $results[user_id]));
    

    $data['organisator'] = [
      '#theme' => 'table',          
      '#rows' => $rows,
    ];
    

    \Drupal\Core\Database\Database::setActiveConnection();             
       
    return data;
    
  }
  public function get_attendees($event,&$header,&$rows){
    $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");
    $query = $database->select('mod_club_user_event' , 'u');    
    $query->join('mod_club_user','u1', 'u.user_id = u1.id');
    $query->fields('u',['event_date_id', 'user_id','unsubscribe_pending']);
    $query->fields('u1',['firstname','lastname','username']);    
    $query->condition('u.event_date_id',$event,'=');
    $header = array('Deelnemers','Afgemeld');
    $results = $query->execute();
    $rows = array();
    foreach ($results as $row => $content){
        $urlname = '/jag/lid/'.$content->username;
        $naam = $content->firstname.' '.$content->lastname;
        $url = '<a href="'.$urlname.'">'.$naam.'<a>';
        $rows[] = array('data' => array(
          Markup::create($url),
          $content->unsubscribe_pending,                                      
                                    ));              
    } 
    
    $data['attendees'] = [
      '#theme' => 'table',          
      '#rows' => $rows,
    ];
    

    \Drupal\Core\Database\Database::setActiveConnection();             
       
    return data;
    
  }
  public function get_ID(&$roles,&$lidtype,&$group_id){
    $cu = \Drupal::currentUser();
    $id = \Drupal::currentUser()->id();
    $account = \Drupal\user\Entity\User::load($id);
    $username = $account->get('name')->value;
    $roles = $cu->getRoles();
    $now = new DateTime('today');

    $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");
// select data §from user
    $query_usr = $database->select('mod_club_user','u');
    $query_usr->fields('u',['id', 'username', 'type','dob']);
    $query_usr->condition('u.username',$username,'=');        
    $results_usr = $query_usr->execute()->fetchAssoc();
    if ($results_usr){            
      $lidtype = $results_usr['type'];
      $gb = $results_usr['dob'];
      $dt_gb = new DateTime($gb);
      $tst = $now->diff($dt_gb);
      $age  = $tst->y;
      $query_ag = $database->select('mod_club_agegroup','a');
      $query_ag->fields('a',['age_from','age_to','name_nl','type']);
      $results_ag = $query_ag->execute();
      $group_id = '9';
      foreach ($results_ag as $row_ag => $content_ag){
        if ( $age >= $content_ag->age_from && $age <= $content_ag->age_to){
          $rows[] = array('data' => array('Groep', $content_ag->name_nl));            
          if ($content_ag->type =='0'){
            $rows[] = array('data' => array('Algemene groep', 'Friends'));            
            $group_id = $content_ag->type;
          }
          if ($content_ag->type == '1'){
            $rows[] = array('data' => array('Algemene groep', 'Plus'));            
            $group_id = $content_ag->type;
          }
        }          
      }
    }
// select dit general security
    $query = $database->select('usergroup_user','u');
    $query->fields('u',['id', 'email','groupid']);
    $query->condition('u.email',$username,'=');    
    $query->condition('u.groupid','1','=');
    $results = $query->execute()->fetchAssoc();
    if ($results){      
      $idvalue = $results['id'];
      return $results['email'];
    } else {
      return -1;
    }

    
  }
}