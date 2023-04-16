<?php
namespace Drupal\jag\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route responses for the Example module.
 */
class TopEventsController extends ControllerBase {
 
  /**
   * Returns a simple page.
   *
   * @return array
   *   Top activitities - tool to reward persons for their organization within the organization (film tickets).
   */
  public function mainPage() {
  
    
  $html ="<h1>Top Activities controller</h1>";    

  $data['#markup'] = [
    '#markup' => $html,
  ];

 
  
   
  return $data;   
     
  }
 

}