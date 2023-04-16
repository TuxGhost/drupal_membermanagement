<?php

namespace Drupal\jag\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TempStore\PrivateTempStoreFactory;

/**
 * Implements an example form.
 */
class JAGAdminForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'JAGAdminForm';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {    
    $form['import_emails'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Importeer e-mails from de JAG database naar het CMS systeem'),
      '#default_value' => $_SESSION["import_emails"],
      '#options' => array(1 => $this->t('import_emails')),             
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
    $database = \Drupal\Core\Database\Database::getConnection("default","dit_jag");
    $strTable = "select * from mod_club_user";
    $strSort = "order by date_created desc";
    $strFilter = 'type = :type';      
    $strType = '1';    
    $aantal = 0;
    
    $strQuery = $strTable;        
    $query = $database->query($strQuery);    
    
    // seems it implementing a prefix.
    $query = $database->select('mod_club_user' , 'u');
    
    $query->fields('u',[username,email,firstname,lastname,type,board_member]);
    $query->condition('u.type',$strType,'=');          
    //$query->condition('u.board_member',1,'=');      // Filter on board members (initial testing)
    $results = $query->execute();

    if(is_null($query)){

    }else{            
      $rows = array();
      foreach ($results as $row => $content){
        $rows[] = array('data' => array(
        $content->username,
        $content->email,
        $content->firstname,
        $content->lastname,
        $content->type));

        $drupal_id = \Drupal::entityQuery('user')
          ->condition('mail', $content->email)
          ->execute();
        if(empty($drupal_id)){
          $user = \Drupal\user\Entity\User::create();

          //Required
          $user->setUsername($content->username);
          $user->enforceIsNew();
          $user->setEmail($content->email);
          $user->setUsername($content->username);
          $user->block();
          //$user->activate();
          $user->save();
          $aantal = $aantal + 1;
        } else{

        }
      }
    }
    $this->messenger()->addStatus($this->t('@number gebruikers werden bij aangemaakt', ['@number' => $aantal]));
    $_SESSION['import_emails'] = $form_state->getValue("import_emails");

  }

}