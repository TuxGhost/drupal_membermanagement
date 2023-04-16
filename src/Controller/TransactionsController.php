<?php
namespace Drupal\jag\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route responses for the Example module.
 */
class TransactionsController extends ControllerBase {

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function mainPage() {
    
    $forms = \Drupal::formBuilder()->getForm('Drupal\jag\Form\TransactiesForm');
    
    $html ="<h1>Transactions controller</h1>";    

    /*$data['#markup'] = [
      '#markup' => $html,
    ];*/

    $data['form'] = [
      'form' => $forms,
    ];

    
    //is the database copies into the test environment
    $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");
    $query = $database->select('mod_club_transaction' , 'u');
    $query->join('mod_club_user','u1','u1.id = u.user_id' );
    $query->fields('u',[ 'id','user_id', 'date_created','amount','type','payment_method','provider_status','date_paid']);
    $query->fields('u1',['firstname','lastname']);
    $query->orderBy('date_created','DESC');

    if(!is_null($_SESSION['search'])){
      $search = $_SESSION['search'];
      if($search != '') {
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
                                      $content->amount,
                                      $content->type,
                                      $content->payment_method,
                                      $content->provider_status,
                                      $content->date_paid,
                                    ));
    }    

    $header = array('ID','Voornaam','Achternaam','Bedrag','type','paymentmethod','Providerstatus','Betaald op');        
    $data['transacties'] = [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
    ];
    
    return $data;

     
  }

}