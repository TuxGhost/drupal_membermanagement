<?php

namespace Drupal\jag\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
//use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Drupal\Core\link;
use Drupal\Core\Render\Markup;

/**
 * Implements an example form.
 */
class LedenBeheerForm extends FormBase {
  
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ledenbeheerform';
  }

  public function getHeader(){
    $header = array('E-mail','Voornaam','Achternaam','Type','Toestemming','Toestemming20','Geregistreerd','Gewijzigd');    
    return $header;
  }
  public function getRows(){
    $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");
    $strTable = "select * from mod_club_user";
    $strSort = "order by date_created desc";
    $strFilter = 'type = :type';      
    $strType = '0';
    if(is_null($_SESSION['typelid'])){
      $strType = '1';
    } else{ 
      $strType = $_SESSION[typelid];
    }
    if(!is_null($_SESSION['bestuur'])){
      $options = $_SESSION['bestuur'];
    }    
    
    $strQuery = $strTable;        
    $query = $database->query($strQuery);
    $query = $database->select('mod_club_user' , 'u');   
   
    $query->fields('u',['email','firstname','lastname','type','agree_list','agree_data_20_years','date_created','date_modified','board_member','ID']);

    $query->condition('u.type',$strType,'=');      
      if($options[1]=="1"){
        $query->condition('u.board_member',1,'=');
      }      
    
    if ($_SESSION["sortering"] == "0" ){      
      $strSort = "order by date_modified desc";
      $query->orderBy('date_created','DESC');

    } elseif($_SESSION["sortering"] == "1"){    
      $strSort = "order by date_created desc";
      $query->orderBy('date_modified','ASC');
    }
    if(!is_null($_SESSION['search'])){
      $search = $_SESSION['search'];
      if($search != '') {        
        $group = $query->orConditionGroup()
          ->condition('u.firstname',$search,'LIKE')
          ->condition('u.lastname',$search,'LIKE'); 
        $query->condition($group);
      }
    }

    $results = $query->execute();

    if(is_null($query)){

    }else{        
      $rows = array();
      foreach ($results as $row => $content){      
        $rows[] = array('data' => array($content->email,
                                        $content->firstname,
                                        $content->lastname,
                                        $content->type,
                                        $content->agree_list,
                                        $content->agree_data_20_years,
                                        $content->date_created,
                                        $content->date_modified,
                                        $content->id));
        
      }      
      $header = array('E-mail','Voornaam','Achternaam','Type','Toestemming','Toestemming20','Geregistreerd','Gewijzigd','ID');
      $data['ledenlijst'] = [
        '#theme' => 'table',
        '#header' => $header,
        '#rows' => $rows,
      ];
      \Drupal\Core\Database\Database::setActiveConnection();   
      

    }
    //$datarows = $rows;
    return $rows;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {    
    //$form = parent::buildForm($form, $form_state);  // abstract inthis.
    //$vals = $form_state->getStorage();
    //$input = $form_state->getValues();
    //if($form_state->isSubmitted() == true) {    

    /*if (hasAccess()){      
    }*/
    $bestuur = ' ';
    $typelid = '1';
    $datelid = '2';
    $sortering = '0';
    $sortering = '1';
    $search = '';

    $input = $form_state->getUserInput();
    if (count($input) > 0){
      $sortering = $input['sortering'];
      $typelid = $input['TypeLid'];
      $search = $input['Search'];
      $action = $input['op'];
      if (in_array('Bestuur',$input)){
        $bestuur = $input['Bestuur'];
      }
    } else {
      if(isset($_SESSION["sortering"])){
        $sortering = $_SESSION["sortering"];
      }
      if(isset($_SESSION["typelid"])){
        $typelid = $_SESSION["typelid"];      
      }
      if(isset($_SESSION["bestuur"])){
        $bestuur = $_SESSION["bestuur"];
      }
      if(isset($_SESSION["search"])){
        $search = $_SESSION["search"];     
      }
      
      $action = 'start';
    }
    $form['sortering'] = [
      '#type' => 'radios',
      '#title' => $this->t('Sortering'),
      '#default_value' => $sortering, //$_SESSION["sortering"],
      '#options' => array(0 => $this->t('Dalend'), 1 => $this->t('Stijgend')),             
      '#multicolumn' => array('width' => 2),
      
    ];
    $form['TypeLid'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('TypeLid'),
      '#default_value' => $typelid, //$_SESSION["typelid"],
      '#options' => array('0' => $this->t('Geregistreerd lid'), '1' => $this->t('Lid'), '2' => $this->t("Oud lid")),             
      '#multicolumn' => array('width' => 3),
    ];
    $form['Bestuur'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Bestuur'),
      '#default_value' => $bestuur, //$_SESSION["bestuur"],
      '#options' => array(1 => $this->t('Bestuur') ),             
    ];
    
    $form['Search'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Zoek op naam'),
      #'#default_value' => $_SESSION["search"],      
      '#default_value' => $search,
    ];


    //$form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Refresh'),
      '#button_type' => 'primary',
    ]; 

    //$form['#theme'] = 'jag';
    /*$tempStore = \Drupal::service('tempstore.private');
    $store = $tempStore->get('leden');
    $leden = $store->get('leden');*/


    if(isset($action)){
      $header = array('E-mail','Voornaam','Achternaam','Type','Toestemming','Toestemming20','Geregistreerd','Gewijzigd');    
      $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");
      $strTable = "select * from mod_club_user";
      $strSort = "order by date_created desc";
      $strFilter = 'type = :type';      
      //$strType = '0';
    
    
      $strQuery = $strTable;        
      $query = $database->query($strQuery);
      $query = $database->select('mod_club_user' , 'u');  
      //$query->join('mod_club_user_subscription','u1');   
      $query->fields('u',[ 'email','firstname','lastname','type','agree_list','agree_data_20_years','date_created','date_modified','board_member','id','username','referrer','referrer_extra']);
      //$query->fields('u1',['date_from','date_to']);
// SQL error on Dackus site -> to be confirmed.
      /*if(isset($typelid)){
        if(!is_array($typelid)){
          $query->condition('u.type',$typelid,'=');      
        }else{
          $grouplid = $query->orConditionGroup();
          foreach ($typelid as $tl){            
            $grouplid->condition('u.type',$tl,'=');
          }
          $query->condition($grouplid);
        }
      }*/

      if(isset($bestuur)){
        if($bestuur[1]=="1"){
          $query->condition('u.board_member',1,'=');
        }
      }      
      if(isset($search)){      
        if($search != '') {        
          $group = $query->orConditionGroup()
            ->condition('u.firstname',$search,'LIKE')
            ->condition('u.lastname',$search,'LIKE'); 
          $query->condition($group);
        }
      }

    
      if ($sortering == "0" ){      
        $strSort = "order by date_created desc";
        $query->orderBy('date_created','DESC');

      } elseif($sortering == "1"){    
        $strSort = "order by date_created desc";
        $query->orderBy('date_created','ASC');
      }

      $results = $query->execute();

      if(is_null($query)){

      }else{        
        $rows = array();
        foreach ($results as $row => $content){
          $urlname = '/jag/lid/'.$content->username;
          $url = '<a href="'.$urlname.'">'.$content->firstname.' '.$content->lastname.'<a>';
          $rows[] = array('data' => array(
                                        Markup::create($url),
                                        $content->firstname,
                                        $content->lastname,
                                        $content->type,
                                        $content->agree_list,
                                        $content->agree_data_20_years,
                                        $content->date_created,
                                        $content->date_modified,
                                        $content->referrer,                                        
                                        $content->referrer_extra,
                                    //  $content->date_to,
                                      ));
        
        }      
        $header = array('E-mail','Voornaam','Achternaam','Type','Toestemming','Toestemming20','Geregistreerd','Gewijzigd','Via','Extra');
        $data['ledenlijst'] = [
          '#theme' => 'table',
          '#header' => $header,
          '#rows' => $rows,
        ];
        \Drupal\Core\Database\Database::setActiveConnection();   
        $_datarows = $rows;      
      }
      $form['ledenlijstje'] = [
        '#theme' => 'table',
        '#header' => $header,
        '#rows' => $rows,
      ];
    }
    
    /*$tempStore = \Drupal::service('tempstore.private');
    $store = $tempStore->get('leden');
    $store->set('leden',$rows);*/
    //setRebuild(FALSE);
    return $form;    
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    /*if (strlen($form_state->getValue('phone_number')) < 3) {
      $form_state->setErrorByName('phone_number', $this->t('The phone number is too short. Please enter a full phone number.'));
      }*/
   
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    //$this->messenger()->addStatus("show members");
    //$this->messenger()->addStatus($this->t('Your phone number is @number', ['@number' => $form_state->getValue('phone_number')]));
    /*$input = $form_state->getValue('phone_number');
    $params['query'] = [
      'e' => $input,
    ];
    $_SESSION['telefoon'] = [$input];*/
    //$form_state->setRedirectUrl(Url::fromUri('internal:' . 'YOUR_ROUTE',$params));
    //$form_state->setRedirectUrl('jag.LedenBheerController');    

    //$_SESSION['start'] = "true";
    $_SESSION['sortering'] = $form_state->getValue("sortering"); // !!! get value sorting naamwijziging
    $selected = array();
    foreach($form_state->getValue("TypeLid") as $a => $b){
      if((string)$b != "0"){
        $selected[] = $b;
      }
    }
    //$_SESSION['typelid'] = $form_state->getValue("TypeLid");
    $_SESSION['typelid'] = $selected;
    $_SESSION['bestuur'] = $form_state->getValue("Bestuur");
    $_SESSION['search'] = $form_state->getValue("Search");
    //variable_set('sortering', $form_state->getValue("sortering"));

    /*$input = $form_state->getUserInput();
    if (count($input) > 0){
      $sortering = $input['sortering'];
      $typelid = $input['TypeLid'];
      $search = $input['Search'];
      $action = $input['op'];
      if (in_array('Bestuur',$input)){
        $bestuur = $input['Bestuur'];
      }
    }*/
    //parent::submitForm($form,$formstate);
    //$this->sub($form,$form_state);
    //$form_state->setRedirect('<front>');
    //$form_state->setCached();
    //parent::submitForm($form, $form_state);
    //$waarden = $form_state->getValues();
    //$form_state->setStorage('data', $waarden);
    //$form_state->setStorage($waarden);
    //$form_state->setStorage('sortering',$form_state->getValue('sortering'));
 //   $form_state['cache'] = true;
  }

  public function hasAccess(){
    return false;
  }
  
}