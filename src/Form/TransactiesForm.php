<?php

namespace Drupal\jag\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TempStore\PrivateTempStoreFactory;

/**
 * Implements an example form.
 */
class TransactiesForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'transactiesform';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {  
    $form['Search'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Zoek op naam'),
      '#default_value' => $_SESSION["search"],      
    ];

    $form['actions']['#type'] = 'actions';
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
    
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {      
    $_SESSION['start'] = "true";
    /*$_SESSION['sortering'] = $form_state->getValue("sortering"); // !!! get value sorting naamwijziging
    $_SESSION['typelid'] = $form_state->getValue("TypeLid");
    $_SESSION['bestuur'] = $form_state->getValue("Bestuur");*/
    $_SESSION['search'] = $form_state->getValue("Search");
  }

}