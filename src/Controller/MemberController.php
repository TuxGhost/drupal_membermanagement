<?php
namespace Drupal\jag\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\AccessInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Datetime\DrupalDateTime;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Provides route responses for the Example module.
 */
class MemberController extends ControllerBase {

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function userProfile($user){   
    $username = $user->getAccountName();
    $forms2 = \Drupal::formBuilder()->getForm('Drupal\jag\Form\ActivateMembershipForm',$username);
    $data = $this->mainPage($username);
    $data['lidmaatschap'] = [
      'form' => $forms2,
    ];       
    //return $this->mainPage($username);
    return $data;
  }
  public function mainPage($lid) {
    $forms = \Drupal::formBuilder()->getForm('Drupal\jag\Form\SingleSubmitForm');    
    $forms2 = \Drupal::formBuilder()->getForm('Drupal\jag\Form\ActivateMembershipForm');
    //$forms2 = \Drupal::formBuilder()->getForm('Drupal\jag\Form\SingleSubmitForm');    
    $cu = \Drupal::currentUser();
    $id = \Drupal::currentUser()->id();
    $account = \Drupal\user\Entity\User::load($id);
    $username = $account->get('name')->value;
    //$ledenbeheer = user_access('member management');

    $html ="<h1>Persoonlijke pagina</h1>";    
  
    //$id = $this->get_ID($ledenbeheer);    
    $id = $this->get_ID($roles);    
    if ($lid <> 'currentuser' || $id <> -1){
      $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");
      $query = $database->select('mod_club_user','u');
      $query->fields('u',['id', 'username', 'email','firstname','lastname','gender','dob','address','zipcode','city','country','telephone','telephone2','type']);
      if ($lid == 'currentuser'){
        $query->condition('u.username',$username,'=');
      } else {
        $query->condition('u.username',$lid,'=');
      }      
      $results = $query->execute()->fetchAssoc();
      if ($results){
        $rows = array();                        
        $rows[] = array('data' => array('Voornaam',$results['firstname']));
        $gb = $results['dob']; 
        $now = new DateTime('today');
        $dt_gb = new DateTime($gb);
        $tst = $now->diff($dt_gb);
        $age  = $tst->y;
        $query_ag = $database->select('mod_club_agegroup','a');
        $query_ag->fields('a',['age_from','age_to','name_nl','type']);
        /*$andGroup_ag = $query_ag->andConditionGroup()
          ->condition('a.age_from',$age,'>=')
          ->condition('a.age_to',$age,'<=');
        $query_ag->condition($andgroup_ag);*/
        $results_ag = $query_ag->execute();

        foreach ($results_ag as $row_ag => $content_ag){
          if ( $age >= $content_ag->age_from && $age <= $content_ag->age_to){
            $rows[] = array('data' => array('Groep', $content_ag->name_nl));            
            if ($content_ag->type =='0'){
              $rows[] = array('data' => array('Algemene groep', 'Friends'));            
            }
            if ($content_ag->type == '1'){
              $rows[] = array('data' => array('Algemene groep', 'Plus'));            
            }
          }          
        }

        # Drupal 9.5 : rol jag ipv jag_ledenbeheer
        if($lid == 'currentuser' || in_array("jag_ledenbeheer",$roles) || in_array("jag",$roles)) {
          $rows[] = array('data' => array('Achternaam',$results['lastname']));
          $rows[] = array('data' => array('Geslacht',$results['gender']));
          $rows[] = array('data' => array('Geboortedatum',$results['dob']));
          $rows[] = array('data' => array('Adres',$results['address']));
          $rows[] = array('data' => array('zipcode',$results['zipcode']));
          $rows[] = array('data' => array('Stad',$results['city']));
          $rows[] = array('data' => array('Telefoon',$results['telephone']));
          $rows[] = array('data' => array('Telefoon',$results['telephone2']));
          switch ($results['type']){
            case '0':
              $rows[] = array('data' => array('Lidmaatschap:','Aspirant'));
              break;
            case '1':
              $rows[] = array('data' => array('Lidmaatschap:','Lid'));
              break;
            case '2':
              $rows[] = array('data' => array('Lidmaatschap:','Oud Lid'));
              $data['form2'] = [
                'form' => $forms2,
              ];   
              break;
            default:
              $rows[] = array('data' => array('Lidmaatschap:','Onbekend lidmaatschap'));                
          }
        }
        $data['waarden'] = [
          '#theme' => 'table',          
          '#rows' => $rows,
        ];
        $data['form'] = [
          'form' => $forms,
        ];   
        /*$data['form2'] = [
          'form' => $forms2,
        ];*/   
        $ret = $this->get_events($results['id'],$header_ev,$rows_ev);      
        \Drupal::service('page_cache_kill_switch')->trigger();
        $data['events'] = [
          '#theme' => 'table',
          '#header' => $header_ev,
          '#rows' => $rows_ev,
        ];


        $formresponse = \Drupal::formBuilder()->getForm('Drupal\jag\Form\EventResponseForm');    

        return $data;
      }
    }
    $data['lijst'] = [
      '#markup' => $html,
    ];
    return $data;
  }
  public function get_ID(&$roles){
    $cu = \Drupal::currentUser();
    $id = \Drupal::currentUser()->id();
    $account = \Drupal\user\Entity\User::load($id);
    $username = $account->get('name')->value;
    $roles = $cu->getRoles();

    $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");
    $query = $database->select('usergroup_user','u');
    $query->fields('u',['id', 'email','groupid']);
    $query->condition('u.email',$username,'=');
    //$query->condition('u.groupid',1,'=');  // System administrator
    $query->condition('u.groupid','1','=');
    $results = $query->execute()->fetchAssoc();
    if ($results){
      //$idvalue = $results->id;
      //return $idvalue;
      /*$rows = array();
      foreach ($results as $row => $content){  
        $test = $content->id;
      }*/
      //return $results->id;
      $idvalue = $results['id'];
      return $results['email'];
    } else {
      return -1;
    }
    
  }

  public function get_events($user_id,&$header,&$rows){
    $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");
    $query = $database->select('mod_club_user_event' , 'u');    
    $query->join('mod_club_user','u1', 'u.user_id = u1.id');    
    $query->join('mod_club_event_date','u2','u2.id = u.event_date_id');
    $query->join('mod_club_event','u3', 'u3.id = u2.event_id ');
    $query->fields('u',['event_date_id', 'user_id']);
    //$query->fields('u1',[firstname,lastname]);    
    $query->fields('u2',['start']);    
    $query->fields('u3',['name_nl']);    
    
    $query->condition('u.user_id',$user_id,'=');
    $query->orderBy('start','DESC');
    $query->range(0,25);
    $header = array('Evenement','datum');
    $results = $query->execute();
    $rows = array();
    foreach ($results as $row => $content){
        $url_base = '/jag/evenementen/'.$content->event_date_id;      
        $url_string = '<a href="'.$url_base.'">'.$content->name_nl.'</a>';
        $rows[] = array('data' => array(
                                      Markup::create($url_string),  
                                      $content->start,
                                    ));              
    } 
    
    $data['evenementen'] = [
      '#theme' => 'table',          
      '#rows' => $rows,
    ];
    

    \Drupal\Core\Database\Database::setActiveConnection();             
       
    return $data;
    
  }


  public function hasAccess(){
    $cu = \Drupal::currentUser();
    $id = \Drupal::currentUser()->id();
    $account = \Drupal\user\Entity\User::load($id);
    
    //return $this->access($account);
    $username = $account->get('name')->value;
    if($username == 'root'){
      $username = 'Peter.Kuda';
    }
    $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");
    $query = $database->select('usergroup_user','u');
    $query->fields('u',['id', 'email','groupid']);
    $query->condition('u.email',$username,'=');
    //$query->condition('u.groupid',1,'=');  // System administrator
    $results = $query->execute()->fetchAssoc();
    if ($results == false){
      return false;
    }    
    return true;
  }
  public function access(AccountInterface $account){
    if (AccessResult::Allowed() == true){
      return AccessResult::Allowed();
    }
    return false;
  }
  
  public function import($user, Request $request)
  {
    /** @var User $user */
    \Drupal::messenger()->addStatus('User ' . $user->getAccountName() . ' should be added to the table');
    //_user_mail_notify('register_no_approval_required', $user);
    //$account = \Drupal\user\Entity\User::load($id);
    //$username = $account->get('name')->value;
    $userid = $user->getAccountName();
    $email = $user->getEmail();


    $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");
    try {
      $query_i = $database->insert('mod_club_user')->fields(
        ['email', 'username','member_number','firstname','lastname','address','zipcode',
          'city','country','telephone','telephone2','info_avond','board_member','board_type','board_function','blocked',
          'active','comments','comments_intern','referrer','referrer_extra','mailinglist_sub_id','date_created',
          'date_modified','dob']);
      $query_i->values([$email,$userid,0,"?","?","?","?","?","?","?","0","0","0","0","0","0","0","drupal import",
        'drupal import',' ',' ','0','2022-12-29','2022-12-29 07:10:56.123','2022-12-29']);
      $query_i->execute();
    } catch(\Exception $ex) {
      \Drupal::messenger()->addStatus('Duplicate key');
    }

    \Drupal\Core\Database\Database::setActiveConnection();  

    $previousUrl = $request->headers->get('referer');
    $response = new RedirectResponse($previousUrl);
    return $response;
  }
  
}