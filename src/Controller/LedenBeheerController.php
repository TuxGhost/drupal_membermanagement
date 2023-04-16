<?php
namespace Drupal\jag\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\AccessInterface;

/**
 * Provides route responses for the Example module.
 */
class LedenBeheerController extends ControllerBase {

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function mainPage() {
    /*if (!hasAccess()){
       return; // No Access
    }*/

    $forms = \Drupal::formBuilder()->getForm('Drupal\jag\Form\LedenBeheerForm');
    
    $html ="<h1>JAG Ledenbeheer</h1>";    

    if ($this->hasAccess() == true){    
                      
      $data['form'] = [
        'form' => $forms,      
      ];

      /*$ret = $this->get_events($user_id,$header_att,$rows_att);      
      $data['attendees'] = [
        '#theme' => 'table',
        '#header' => $header_att,
        '#rows' => $rows_att,
      ];*/

      
    } else {
      $html = "<h>U bent niet bevoegd voor de module Ledenbeheer (vanuit de oorspronkelijke database)";
      $data['lijst'] = [
        '#markup' => $html,
      ];
    }
    
    return $data;
  }

 



  public function hasAccess(){
    //$an = \Drupal::getAccountName();
    $cu = \Drupal::currentUser();
    //$sa = $this->access($cu); // 
    $id = \Drupal::currentUser()->id();
    $account = \Drupal\user\Entity\User::load($id);
    $username = $account->get('name')->value;
    if($username == 'root'){
      $username = 'Peter.Kuda';
    }
    $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");
    $query = $database->select('usergroup_user','u');
    $query->fields('u',['id', 'email','groupid']);
    $query->condition('u.email',$username,'=');
    $query->condition('u.groupid',1,'=');  // System administrator
    $results = $query->execute()->fetchAssoc();
    if ($results == false){
      return true; // GDPR error
    }    
    return true;
    
  }
  public function access(AccountInterface $account){    
    return AccessResult::allowedIf($account->hasPermission('access content') && $this->hasAcces());  
  }
}