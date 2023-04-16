<?php
namespace Drupal\jag\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route responses for the Example module.
 */
class AbonnementenController extends ControllerBase {
 
  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function mainPage() {
    $forms = \Drupal::formBuilder()->getForm('Drupal\jag\Form\AbonnementenForm');
    
    $html ="<h1>AbonnementenController controller</h1>";    

    /*$data['#markup'] = [
      '#markup' => $html,
    ];*/

    $data['form'] = [
      'form' => $forms,
    ];

    $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");
    $query = $database->select('mod_club_user_subscription' , 'u');
      $query->join('mod_club_user','u1','u1.id = u.user_id' );
      $query->fields('u',[ 'id','user_id', 'date_from','date_to']);
      $query->fields('u1',['firstname','lastname']);
      $query->orderBy('date_from','DESC');

      if(!is_null($_SESSION['search'])){
        $search = $_SESSION['search'];
        if(empty($search)){
        } else {
          //$query->condition('u.lastname',$search,'LIKE'); ' works
          $group = $query->orConditionGroup()
            ->condition('u1.firstname',$search,'LIKE')
            ->condition('u1.lastname',$search,'LIKE'); 
          $query->condition($group);
        }
      }

      $query->range(0,250);
      $results = $query->execute();
      
      $rows = array();
      foreach ($results as $row => $content){
        $rows[] = array('data' => array($content->id,
                                        $content->firstname,
                                        $content->lastname,
                                        $content->date_from,
                                        $content->date_to,
                                        ));
      }    

      $header = array('ID','Voornaam','Achternaam','van','tot');        
      $data['abonnementen'] = [
        '#theme' => 'table',
        '#header' => $header,
        '#rows' => $rows,
      ];
    
    
    return $data;   
      
    }
 

}