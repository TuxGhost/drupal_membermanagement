<?php
namespace Drupal\jag\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route responses for the Example module.
 */
class JAGAdminController extends ControllerBase {

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function mainPage() {
    
    $forms = \Drupal::formBuilder()->getForm('Drupal\jag\Form\JAGAdminForm');
    
    $html ="<h1>JAG Admin controller</h1>";    
    
    $data['form'] = [
      'form' => $forms,      
    ];

    $data['#markup'] = [
      '#markup' => $html,
    ];

    //$ids = \Drupal::entityQuery('user')
      //->execute();

    $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");
    $strTable = "select * from mod_club_user";
    $strSort = "order by date_created desc";
    $strFilter = 'type = :type';      
    $strType = '1';    
    
    $strQuery = $strTable;        
    $query = $database->query($strQuery);    
    
    // seems it implementing a prefix.
    $query = $database->select('mod_club_user' , 'u');
    
    $query->fields('u',[username,email,firstname,lastname,type,board_member]);
    $query->condition('u.type',$strType,'=');          
    $query->condition('u.board_member',1,'=');      
    $results = $query->execute();

    if(is_null($query)){

    }else{            
      $rows = array();
      foreach ($results as $row => $content){
        $rows[] = array('data' => array(
        $content->username,
        $content->email,
        $content->firstname,
        $content->lastname,
        $content->type));

        $drupal_id = \Drupal::entityQuery('user')
          ->condition('mail', $content->email)
          ->execute();
        if(empty($drupal_id)){
          $user = \Drupal\user\Entity\User::create();

          //Required
          $user->setUsername($content->username);
          $user->enforceIsNew();
          $user->setEmail($content->email);
          $user->setUsername($content->username);
          $user->block();
          //$user->activate();
          $user->save();
        } else{

        }
      }
    }
    return $data;

     
  }

}