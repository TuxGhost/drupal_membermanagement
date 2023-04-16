<?php
namespace Drupal\jag\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route responses for the Example module.
 */
class JAGBeheer extends ControllerBase {

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function mainPage() {
    
    $forms = \Drupal::formBuilder()->getForm('Drupal\jag\Form\JAGAdminForm');
    
    $html ="<h1>Template controller</h1>";    

    $data['#markup'] = [
      '#markup' => $html,
    ];

    $data['form'] = [
      'form' => $forms,
    ];

    /*$database = \Drupal::database();
    $query = $database->query("select name_nl , event_start from mod_club_event order by event_start desc");
    $results = $query->fetchAll();    
    $rows = array();
    foreach ($results as $row => $content){
      $rows[] = array('data' => array($content->name_nl,
                                      $content->event_start));
    }    
    $header = array('Evenement','datum');
        
    $data['evenementen'] = [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
    ];*/
    
    return $data;

     
  }

}