<?php
namespace Drupal\jag\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\AccessInterface;


/**
 * Provides route responses for the Example module.
 */
class EventController extends ControllerBase {

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function mainPage($event) {
    //$forms = \Drupal::formBuilder()->getForm('Drupal\jag\Form\LedenBeheerForm');    
    //$cu = \Drupal::currentUser();
    //$id = \Drupal::currentUser()->id();
    //$account = \Drupal\user\Entity\User::load($id);
    //$username = $account->get('name')->value;

    //$html ="<h1>Persoonlijke pagina</h1>";    
  
    //$id = $this->get_ID();    
    if ($lid == -1){
      $html = "<p>Geen evenement geselecteerd</p>";
    } else {
      $html = "<p>Evenement geselecteerd</p>";
      /*$database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");
      $query = $database->select('mod_club_user','u');
      $query->fields('u',[id, username, email,firstname,lastname,gender,dob,address,zipcode,city,country,telephone,telephone2,type,]);
      if ($lid == 'currentuser'){
        $query->condition('u.username',$username,'=');
      } else {
        $query->condition('u.username',$lid,'=');
      }
      $results = $query->execute()->fetchAssoc();
      if ($results){
        $rows = array();                
        $rows[] = array('data' => array('Voornaam',$results[firstname]));
        $rows[] = array('data' => array('Achternaam',$results[lastname]));
        $rows[] = array('data' => array('Geslacht',$results[gender]));
        $rows[] = array('data' => array('Geboortedatum',$results[dob]));
        $rows[] = array('data' => array('Adres',$results[address]));
        $rows[] = array('data' => array('zipcode',$results[zipcode]));
        $rows[] = array('data' => array('Stad',$results[city]));
        $rows[] = array('data' => array('Telefoon',$results[telephone]));
        $rows[] = array('data' => array('Telefoon',$results[telephone2]));
        $data['waarden'] = [
          '#theme' => 'table',          
          '#rows' => $rows,
        ];
        return $data;
      }*/
    }
    $data['lijst'] = [
      '#markup' => $html,
    ];



    return $data;
  }
/*  public function get_ID(){
    $cu = \Drupal::currentUser();
    $id = \Drupal::currentUser()->id();
    $account = \Drupal\user\Entity\User::load($id);
    $username = $account->get('name')->value;

    $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");
    $query = $database->select('usergroup_user','u');
    $query->fields('u',[id, email,groupid]);
    $query->condition('u.email',$username,'=');

    $query->condition('u.groupid','1','=');
    $results = $query->execute()->fetchAssoc();
    if ($results){
      $idvalue = $results['id'];
      return $results['email'];
    } else {
      return -1;
    }
    
  }*/
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
    $query->fields('u',[id, email,groupid]);
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
}