<?php
namespace Drupal\jag\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\Markup;

/**
 * Provides route responses for the Example module.
 */
class WallController extends ControllerBase {
 
  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function mainPage() {
                              
      $ret = $this->get_wall_messages($header,$rows);      
      $data['attendees'] = [
        '#theme' => 'table',
        '#header' => $header,
        '#rows' => $rows,
      ];
    
    return $data;

     
  }  
  public function get_wall_messages(&$header,&$rows){
    $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");
    $query = $database->select('mod_club_wallpost' , 'u');        
    $query->fields('u',['text']);
    $query->orderBy('date_modified','desc');
    $query->range(0,50);
    $header = array('Berichten');    
    $results = $query->execute();
    $rows = array();
    foreach ($results as $row => $content){              
        $rows[] = array('data' => array(
                                      $content->text,                                      
                                    ));              
    } 
    
    $data['wallpost'] = [
      '#theme' => 'table',          
      '#rows' => $rows,
    ];
    

    \Drupal\Core\Database\Database::setActiveConnection();             
       
    return $data;
    
  }
}