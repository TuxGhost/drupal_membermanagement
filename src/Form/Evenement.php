<?php

namespace Drupal\jag\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Datetime\DrupalDateTime;
use DateTime;

/**
 * Implements an example form.
 */
class Evenement extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'Evenement';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state,$event = NULL) {
    // get Member
    $jag_id = $this->getMember();
    // get detail evenement
    $event_id = $this->get_event($event,$header,$rows);     
    //$form_state['event_id'] = $event_id;
    $form['evenementen'] = [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
    ];
    // Get organisator
    $ret = $this->get_organisator($event_id,$header_org,$rows_org);      
    $form['organisator'] = [
      '#theme' => 'table',
      '#header' => $header_org,
      '#rows' => $rows_org,
    ];
    // get attendees
    $aangemeld = $this->get_attendees($event,$header_att,$rows_att,$jag_id);      
    $form['attendees'] = [
      '#theme' => 'table',
      '#header' => $header_att,
      '#rows' => $rows_att,
    ];

    if ($aangemeld == true){
      $label_deelnemen = 'Afmelden';
    } else {
      $label_deelnemen = 'Deelnemen';
    }
    $form['actions']['#type'] = 'actions';    
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Overzicht'),
      '#button_type' => 'primary',      
    ];
    $form['actions']['deelnemen'] = [
        '#type' => 'submit',
        '#value' => $this->t($label_deelnemen),
        '#submit' => ['::attend'],
        '#button_type' => 'primary',
      ];  
    $form_state->set('event_id',$event);
    $form_state->set('aangemeld',$aangemeld);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (1 == 1){
      
    }
  }
  public function getMember(){
    $cu = \Drupal::currentUser();    
    $id = \Drupal::currentUser()->id();
    $account = \Drupal\user\Entity\User::load($id);
    $username = $account->get('name')->value;

    $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");
    $query = $database->select('mod_club_user','u');
    $query->fields('u',['id', 'email','username']);
    $query->condition('u.username',$username,'=');    
    $results = $query->execute()->fetchAssoc();
    $jag_id = $results['id'];
    return $jag_id;
    \Drupal\Core\Database\Database::setActiveConnection();       
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    //$this->messenger()->addStatus($this->t('Your phone number is @number', ['@number' => $form_state->getValue('phone_number')]));
    //\session_destroy();
    $_SESSION['DateFrom'] = $form_state->getValue("van");
    $_SESSION['DateUntil'] = $form_state->getValue("tot");
    $form_state->setRedirect('evenementen.content');
  }
  public function attend(array &$form, FormStateInterface $form_state){
    $event_id = $form_state->get('event_id');
    $aangemeld = $form_state->get('aangemeld');
    $cu = \Drupal::currentUser();    
    $id = \Drupal::currentUser()->id();
    $account = \Drupal\user\Entity\User::load($id);
    $username = $account->get('name')->value;
    $user_id = 0;
    $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");

    $query = $database->select('mod_club_user','u');
    $query->fields('u',['id', 'email','username']);
    $query->condition('u.username',$username,'=');    
    $results = $query->execute()->fetchAssoc();
    $jag_id = $results['id'];

    $query_attend = $database->select('mod_club_user_event','u');
    $query_attend->fields('u',['user_id', 'event_date_id','attend_date','unsubscribe_pending']);
    $query_attend->condition('u.user_id',$jag_id,'=');
    $query_attend->condition('u.event_date_id',$event_id,'=');  // System administrator
    $results_attend = $query_attend->execute()->fetchAssoc();
    $dt = \Drupal::time()->getRequestTime();
    $date_time = new DrupalDateTime($date_value, new \DateTimeZone('UTC'));
    $dt2 = \Drupal::time()->getCurrentTime();
    $dt3 = date("Y-m-d H:i:s", time());
    $values = [
        [
            'user_id' => $jag_id,
            'event_date_id' => $event_id,
            'attend_date' => $dt3,                
            'unsubscribe_pending' => '0',
        ],
    ];
    if ($results_attend == false){
        $query_i = $database->insert('mod_club_user_event')->fields(['user_id', 'event_date_id','attend_date','unsubscribe_pending']);
        foreach ($values as $record){
            $query_i->values($record);
        }
        $query_i->execute();
    } else {
      $unsubscribe_pending = $results_attend['unsubscribe_pending'];
      if ($unsubscribe_pending == "0"){
        $unsubscribe_pending = "1";
      } elseif ($unsubscribe_pending == "1"){
        $unsubscribe_pending = "0";
      }
      $num_updated = $database->update('mod_club_user_event')
        ->fields([
          'user_id' => $jag_id,
          'event_date_id' => $event_id,
          'attend_date' => $dt3,
          'unsubscribe_pending' => $unsubscribe_pending,
        ])
          ->condition('user_id', $jag_id, '=')
          ->condition('event_date_id', $event_id, '=')
        ->execute();
    }
    \Drupal\Core\Database\Database::setActiveConnection();       

  }
  public function get_event($event,&$header,&$rows){
    $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");    
    $query = $database->select('mod_club_event_date' , 'u');    
    $query->join('mod_club_event','u1', 'u.event_id = u1.id');
    $query->fields('u',['id','event_id', 'start', 'end', 'attend_till' , 'cancelled_on']);
    $query->fields('u1',['name_nl','address','zipcode','city','description_nl','creator']);    
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
      $rows[] = array('data' => array('Omschrijving',Markup::create($results['description_nl'])));

      $data['waarden'] = [
        '#theme' => 'table',          
        '#rows' => $rows,
      ];
    
      \Drupal\Core\Database\Database::setActiveConnection();             
    
     //return $results[creator];
     return $results['event_id'];
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
    $naam = $results['firstname'].' '.$results['lastname'];
    $rows = array();                
    $urlname = '/jag/lid/'.$results['username'];    
    $url = '<a href="'.$urlname.'">'.$naam.'<a>';

    $rows[] = array('data' => array(Markup::create($url)));

    $data['organisator'] = [
      '#theme' => 'table',          
      '#rows' => $rows,
    ];
    

    \Drupal\Core\Database\Database::setActiveConnection();             
       
    return $data;
    
  }
  public function get_attendees($event,&$header,&$rows,$jag_id){
    $aangemeld = false;
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
        if ($content->user_id == $jag_id){
          //$aangemeld = true;
          if ($content->attendunsubcribe_pending == 0){
            $aangemeld = True;
          } else {
            $aangemeld = False;
          }          
        }

        $urlname = '/jag/lid/'.$content->username;
        $naam = $content->firstname.' '.$content->lastname;
        $url = '<a href="'.$urlname.'">'.$naam.'<a>';
        $rows[] = array('data' => array(
          Markup::create($url),
          $content->unsubscribe_pending,                                      
                                    ));              
    } 
    
    /*$data['attendees'] = [
      '#theme' => 'table',          
      '#rows' => $rows,
    ];*/
    

    \Drupal\Core\Database\Database::setActiveConnection();             
       
    return $aangemeld;
    
  }
}