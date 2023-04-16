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
class ActivateMembershipForm extends FormBase {
  
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'activatemembershipform';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state,$args = NULL) {  
    $lidmaatschap = false;
    if ($args != null ){
      $username = $args;  
      $msg = 'Activate memberschip for '.$username;
      $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");
      $result = $database->select('mod_club_user','g')
        ->fields('g',[ 'username',])
        ->condition('username', $username, '=')              
        ->execute();
 // Todo : display membership on screen
      
      if(!$result){
        /*$form['membership'] = array(          
          'lidmaatschap' => t('Geen lid')
        );*/
        $msg = 'Deactivate memberschip for '.$username;
      }else{
        $lidmaatschap=true;
        /*$form['membership'] = array(
          'type'  => 'text',
          'lidmaatschap' => t('Lid of oud lid')
       );
       */
      }
      \Drupal\Core\Database\Database::setActiveConnection();    

      $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t($msg),
        '#button_type' => 'primary',
      ]; 
    } else{
      $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Activate membership'),
        '#button_type' => 'primary',
      ]; 
    }

  
    return $form;    
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

   
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    //$_SESSION['sortering'] = $form_state->getValue("sortering"); // !!! get value sorting naamwijziging
    /*$selected = array();
    foreach($form_state->getValue("TypeLid") as $a => $b){
      if((string)$b != "0"){
        $selected[] = $b;
      }
    } */   
    /*$_SESSION['typelid'] = $selected;
    $_SESSION['bestuur'] = $form_state->getValue("Bestuur");
    $_SESSION['search'] = $form_state->getValue("Search");*/

    /*$result = $database->select('mod_club_user','g')
        ->fields('g',[ 'username',])
        ->condition('username', $username, '=')                      
        ->execute();*/

    $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");
    $num_updated = $database->update('mod_club_user')
    ->fields([              
      'type' => '1',
    ])
      ->condition('username', 'Peter.Kuda', '=')              
    ->execute();
    \Drupal\Core\Database\Database::setActiveConnection();    
  }

  public function hasAccess(){
    return false;
  }
  
}