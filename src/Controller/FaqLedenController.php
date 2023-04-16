<?php
namespace Drupal\jag\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route responses for the Example module.
 */
class FaqLedenController extends ControllerBase {
 
  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function mainPage() {
/*    
  Table Page key id 21 - FAQ - 
  table Page_moduleinstance 10 and 43
         moduleinstance ID 43 (= article id) order 1
         moduleinstance id 10 order 9

*/
    $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");    
    $query = $database->select('mod_articles' , 'u');
    //$query->leftJoin('mod_club_event','u1', 'u1.event_id = u.id');
    //$query->join('mod_club_event','u1', 'u.event_id = u1.id');
    $query->fields('u',['articles_id','title_nl','articles_id']);
    $query->condition('u.articles_id',43,'=');

                
    $results = $query->execute();
    $rows = array();
    foreach ($results as $row => $content){
      $rows[] = array('data' => array($content->articles_id,                                      
                                      $content->title_nl,
                                    ));
    }    
    
    $query = $database->select('mod_articles_article' , 'u');
    //$query->leftJoin('mod_club_event','u1', 'u1.event_id = u.id');
    //$query->join('mod_club_event','u1', 'u.event_id = u1.id');
    $query->fields('u',['article_id','title_nl','articles_id']);
    $query->condition('u.articles_id',43,'=');
                
    $results = $query->execute();
    $rows = array();
    foreach ($results as $row => $content){
      $rows[] = array('data' => array(                                      
                                      $content->title_nl,
                                    ));
    }    

    $header = array('Titel');    
    
    /*$data['#markup'] = [
      '#theme' => 'jag',
      '#title' => 'FAQ Leden 2',
      '#header' => $header,
      '#data' => $rows
    ];*/

    $data['evenementen'] = [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
    ];

    return $data;     
    
    /*return [
      '#theme' => 'jag',
      '#title' => 'Faq leden',
      '#header' => $header,
      '#data' => $rows
    ];*/
  }
 
}