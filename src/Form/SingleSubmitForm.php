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
class SingleSubmitForm extends FormBase {
  
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'singlesubmitform';
  }



  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {    
  
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Start'),
      '#button_type' => 'primary',
    ]; 

  
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