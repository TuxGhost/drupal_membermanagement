<?php
namespace Drupal\jag\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route responses for the Example module.
 */
class NewsController extends ControllerBase {
 
  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function mainPage() {
  
    
  $html ="<h1>News controller</h1>";    

  $data['#markup'] = [
    '#markup' => $html,
  ];

 
  
   
  return $data;   
     
  }
 

}