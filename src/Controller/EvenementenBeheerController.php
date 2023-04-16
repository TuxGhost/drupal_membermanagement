<?php
namespace Drupal\jag\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route responses for the Example module.
 */
class EvenementenBeheerController extends ControllerBase {
 
  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function mainPage($event) {
    $forms = \Drupal::formBuilder()->getForm('Drupal\jag\Form\EvenementenBeheerForm');
    
    $html ="<h1>JAG Evenementenbeheer</h1>";    
                
    $data['form'] = [
      'form' => $forms,
    ];    
    if ($this->hasAccess() == false){
      return $data;
    }
    $id = \Drupal::currentUser()->id();
    $account = \Drupal\user\Entity\User::load($id);
    $username = $account->get('name')->value;
    
    $dtestart = $_SESSION['DateFrom'];    
    $dteEnd = $_SESSION['DateUntil'];

    $dte_start = \date_create($dteStart);
    $dte_end = \date_create($dteEnd);

    $dte_start2 = $dte_start->format('Y-m-d');
    $dte_end2 = $dte_start->format('Y-m-d');

    if (is_null($dtestart)){
      $dtestart = $dte_start2;
    }

    $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");
    $query = $database->select('mod_club_event_date','u');
    $query->join('mod_club_event','u1','u1.id = u.event_id' );
    $query->join('mod_club_event_agegroup','u2','u2.event_id = u.event_id');
    $query->join('mod_club_agegroup','u3','u3.id = u2.agegroup_id');
    //$query->join('mod_club_event_agegroup','u2','u2.event_id = u.event_id');  // too many results
    $query->fields('u',[id, event_id,start,end]);
    $query->fields('u1',['name_nl',city]);
    $query->fields('u2',[agegroup_id]); 
    $query->fields('u3',[name_nl]);


    $query->condition('u.start',$dtestart,'>');
    $query->orderBy('start','ASC');
        
    $results = $query->execute();
    $rows = array();
    $firstline = false;
    $agegroup = '';
    $id = ' ';
    foreach ($results as $row => $content){            
      if ($content->id != $id){
        $rows[] = array('data' => array(
                                      $content->start,
                                      $content->name_nl,
                                      $content->city,
                                      $content->u3_name_nl
                                      ));
      } else{
        $rows[count($rows) -1]['data'][3] = $content->u3_name_nl.$rows[count($rows) -1]['data'][3];
      }
      $id = $content->id;
    }    
    $header = array('Start','Omschrijving','locatie','Leeftijdsgroep');
        
    $data['evenementen'] = [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
    ];
    \Drupal\Core\Database\Database::setActiveConnection();      
    return $data;     
  }

  public function hasAccess(){    
    $id = \Drupal::currentUser()->id();
    $account = \Drupal\user\Entity\User::load($id);
    $username = $account->get('name')->value;
    if($username == 'root'){
      $username = 'Peter.Kuda';
    }
    $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");
    $query = $database->select('usergroup_user','u');
    $query->fields('u',[id, email,groupid]);
    $query->condition('u.email',$username,'=');
    //$query->condition('u.groupid',1,'=');  // System administrator
    $results = $query->execute()->fetchAssoc();
    if ($results == false){
      return false;
    }    
    return true;
  }

}