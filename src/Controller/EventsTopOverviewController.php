<?php
namespace Drupal\jag\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\AccessInterface;
use Drupal\Core\Render\Markup;


/**
 * Provides route responses for the Example module.
 */
class EventsTopOverviewController extends ControllerBase {

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function mainPage() {
    $ret = $this->get_data2($header,$rows);      
    $data['topevents'] = [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
    ];
    return $data;
  }

  public function get_data(&$header,&$rows){
    
    $dteStart = $_SESSION['DateFrom'];    
    $dteEnd = $_SESSION['DateUntil'];
   
    $dte_start = \date_create($dteStart);
    $dte_end = \date_create($dteEnd);
    $dte_start2 = $dte_start->format('Y-m-d');
    $dte_end2 = $dte_start->format('Y-m-d');   
    $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");    
    $query = $database->select('mod_club_event_date' , 'u');        
    $query->join('mod_club_event','u1', 'u.event_id = u1.id');
    //$query->join('mod_club_event_agegroup','u2','u2.event_id = u.event_id');    
    $query->join('mod_club_user','u2', "u2.id = u1.creator");
    $query->fields('u',[id,event_id, start, end, attend_till , cancelled_on]);
    $query->fields('u1',[name_nl,creator]);
    $query->fields('u2',[firstname,lastname]); 
    

    $dte_start = \date_create(date('Y').'-01-01');
    //$test = strtotime($dte_start);

    $dte_start2 = $dte_start->format('Y-m-d');
    $dte_end2 = $dte_start->format('Y-m-d');

    if (is_null($dtestart)){
      $dtestart = $dte_start2;
    }
    
    $query->condition('u.start',$dtestart,'>');      

    $query->orderBy('start','ASC');  //old selection parameter
    $query->orderBy('event_id');
    //$query->groupBy('creator');
            
    $results = $query->execute();
    $rows = array();
    $header = array('Omschrijving','Start','Einde','Geannuleerd op','leeftijdsgroep');
    $id = ' ';
    foreach ($results as $row => $content){
      
        $url_base = '/jag/evenementen/'.$content->id;
        $url_string = '<a href="'.$url_base.'">'.$content->name_nl.'</a>';


        $rows[] = array('data' => array(Markup::create($url_string),                                      
                                      $content->start,
                                      $content->end,
                                      $content->cancelled_on,                                                                            
                                      $content->firstname,
                                    ));
        $id = $content->id;
       
    } 
    \Drupal\Core\Database\Database::setActiveConnection();             
    return 0;
  }
  public function get_data2(&$header,&$rows){
    
    $dteStart = $_SESSION['DateFrom'];    
    $dteEnd = $_SESSION['DateUntil'];
   
    $dte_start = \date_create($dteStart);
    $dte_end = \date_create($dteEnd);
    $dte_start2 = $dte_start->format('Y-m-d');
    $dte_end2 = $dte_start->format('Y-m-d');   
    $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");    
    $query = $database->select('mod_club_event_date' , 'u');        
    $query->join('mod_club_event','u1', 'u.event_id = u1.id');
    //$query->join('mod_club_event_agegroup','u2','u2.event_id = u.event_id');    
    $query->join('mod_club_user','u2', "u2.id = u1.creator");
    //$query->fields('u',[event_id]);
    $query->fields('u1',[creator]);
    $query->fields('u2',[firstname,lastname]); 
    $query->addExpression('COUNT(event_id)','count');
    
    $dte_start = \date_create(date('Y').'-01-01');
    //$test = strtotime($dte_start);

    $dte_start2 = $dte_start->format('Y-m-d');
    $dte_end2 = $dte_start->format('Y-m-d');

    if (is_null($dtestart)){
      $dtestart = $dte_start2;
    }
    
    $query->condition('u.start',$dtestart,'>');      

    $query->orderBy('start','ASC');  //old selection parameter
    //$query->orderBy('event_id');
    $query->groupBy('creator , firstname , lastname');
            
    $results = $query->execute();
    $rows = array();
    $header = array('Naam','Aantal');
    $id = ' ';
    foreach ($results as $row => $content){
      
        $url_base = '/jag/evenementen/'.$content->id;
        $url_string = '<a href="'.$url_base.'">'.$content->name_nl.'</a>';


        $rows[] = array('data' => array(                                                                          
                                      $content->firstname.' '.$content->lastname,
                                      $content->count,
        ));
        $id = $content->id;
       
    } 
    \Drupal\Core\Database\Database::setActiveConnection();             
    return 0;
  }
}