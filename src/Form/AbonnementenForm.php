<?php

namespace Drupal\jag\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TempStore\PrivateTempStoreFactory;

/**
 * Implements an example form.
 */
class AbonnementenForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'abonnementenform';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
   /* $form['sortering'] = [
      '#type' => 'radios',
      '#title' => $this->t('Sortering'),
      '#default_value' => $_SESSION["sortering"],
      '#options' => array(0 => $this->t('Dalend'), 1 => $this->t('Stijgend')),             
    ];*/
    /**$form['TypeLid'] = [
      '#type' => 'radios',
      '#title' => $this->t('TypeLid'),
      '#default_value' => $_SESSION["typelid"],
      '#options' => array(0 => $this->t('Geregistreerd lid'), 1 => $this->t('Lid'), 2 => $this->t("Oud lid")),             
    ];*/
    /*$form['Bestuur'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Bestuur'),
      '#default_value' => $_SESSION["bestuur"],
      '#options' => array(1 => $this->t('Bestuur')),             
    ];*/
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
    /*if (strlen($form_state->getValue('phone_number')) < 3) {
      $form_state->setErrorByName('phone_number', $this->t('The phone number is too short. Please enter a full phone number.'));
    }*/
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    //$this->messenger()->addStatus($this->t('Your phone number is @number', ['@number' => $form_state->getValue('phone_number')]));
    /*$input = $form_state->getValue('phone_number');
    $params['query'] = [
      'e' => $input,
    ];
    $_SESSION['telefoon'] = [$input];*/
    //$form_state->setRedirectUrl(Url::fromUri('internal:' . 'YOUR_ROUTE',$params));
    //$form_state->setRedirectUrl('jag.LedenBheerController');    

    $_SESSION['start'] = "true";
    /*$_SESSION['sortering'] = $form_state->getValue("sortering"); // !!! get value sorting naamwijziging
    $_SESSION['typelid'] = $form_state->getValue("TypeLid");
    $_SESSION['bestuur'] = $form_state->getValue("Bestuur");*/
    $_SESSION['search'] = $form_state->getValue("Search");
  }

}