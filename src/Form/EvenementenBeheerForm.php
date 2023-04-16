<?php

namespace Drupal\jag\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements an example form.
 */
class EvenementenBeheerForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'EvenementenBeheerForm';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $dteStart =  time(); //date('Y-m-d',time());
    $year = date('Y',$dteStart);
    $dteEnd = date('Y-m-d', mktime(23,59,59,12,31,date('Y',$dteStart)));
    $dteStart = date('Y-m-d',$dteStart);
    $form['van'] = [
      '#type' => 'date',
      '#title' => $this->t('Vanaf datum'),
      '#default_value' => $dteStart,
    ];
    /*$form['tot'] = [
      '#type' => 'date',
      '#title' => $this->t('Tot  datum'),
      '#default_value' => $dteEnd,
    ];*/    
    $form['actions']['#type'] = 'actions';    
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Start'),
      '#button_type' => 'primary',      
    ];
    $form['submit'] = [
       '#type' => 'submit',
       '#value' => $this->t('submit'),
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
    if (1 == 1){
      
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    //$this->messenger()->addStatus($this->t('Your phone number is @number', ['@number' => $form_state->getValue('phone_number')]));
    //\session_destroy();
    $_SESSION['DateFrom'] = $form_state->getValue("van");
    $_SESSION['DateUntil'] = $form_state->getValue("tot");
  }

}