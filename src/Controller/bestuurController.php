<?php
namespace Drupal\jag\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\Markup;

/**
 * Provides route responses for the Example module.
 */
class BestuurController extends ControllerBase {
 
  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function mainPage() {

/*$database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");    
$query = $database->select('mod_articles' , 'u');
//$query->leftJoin('mod_club_event','u1', 'u1.event_id = u.id');
//$query->join('mod_club_event','u1', 'u.event_id = u1.id');
$query->fields('u',[articles_id,title_nl,articles_id]);
$query->condition('u.articles_id',[23,106,101],'IN');

            
$results = $query->execute();
$rows = array();
foreach ($results as $row => $content){
  $rows[] = array('data' => array(                                    
                                  $content->title_nl,
                                ));
}    


$header = array('Titel');    

$data['evenementen'] = [
  '#theme' => 'table',
  '#header' => $header,
  '#rows' => $rows,
];*/
    $ret = $this->get_board_members($header,$rows);      
    $data['attendees'] = [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
    ];

    return $data;     
     
  }

  public function get_board_members(&$header,&$rows){
    $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");    
    $query = $database->select('mod_articles' , 'u');
    $query->join('mod_articles_article','u1', 'u1.article_id = u.articles_id');
    //$query->join('mod_club_event','u1', 'u.event_id = u1.id');
    $query->fields('u',['articles_id','title_nl','articles_id']);
    $query->fields('u1',['intro_nl']);
    //$query->condition('u.articles_id',[23,106,101],'IN');
    $query->condition('u.articles_id',[23,106],'IN');
                    
    $results = $query->execute();
    $rows = array();
    foreach ($results as $row => $content){
      $rows[] = array('data' => array(                                    
                                      $content->title_nl,
                                    ));
      $rows[] = array('data' => array(                                    
        Markup::create($content->intro_nl),
                                    ));
    }    
        
    $header = array('Bestuur');    
    
    $data['board_members'] = [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
    ];
    return $data;
    
  }
 
}

