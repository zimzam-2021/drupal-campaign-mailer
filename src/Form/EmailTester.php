<?php

namespace Drupal\controlpanel\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

/**
 * Implements an example form.
 * 
 * 
 */



class EmailTester extends FormBase {

    private $emailSettings = [
        'gmail' => [
            'smtp_server'=>'smtp.gmail.com',
            'port'=>'587',
            'protocol'=>'tls'
        ],
        'yandex' => [
            'smtp_server'=>'smtp.yandex.com',
            'port'=>'465',
            'protocol'=>'ssl'
        ],
        'outlook' => [
            'smtp_server'=>'smtp.office365.com',
            'port'=>'587',
            'protocol'=>'tls'
        ],
        'rediff' => [
            'smtp_server'=>'smtp.rediffmail.com',
            'port'=>'25',
            'encryption'=> 'false'
        ],
        'yahoo' => [
            'smtp_server'=>'smtp.mail.yahoo.com',
            'port'=>'465',
            'protocol'=>'ssl'
        ],
        'aol' => [
            'smtp_server'=>'smtp.aol.com',
            'port'=>'465',
            'protocol'=>'ssl'
        ],
    ];

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'email_tester';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    
    $form['file_modified'] = ['#markup' =>
        '<div>Last Modified - '.gmdate("Y-m-d\TH:i:s\Z", filemtime(__FILE__)).'</div>'];

    $ajaxSettings = [
        'callback' => '::myAjaxCallback',
        'event' => 'change',
        'wrapper' => 'container', // This element is updated with this AJAX callback.
        'progress' => [
        'type' => 'throbber',
        ],
    ];
    
    $userInputs = $form_state->getUserInput();
    $smtpSettings = !empty($userInputs['smtp_settings']) ? $userInputs['smtp_settings'] : 'gmail';
    $defaultValues = !empty($this->emailSettings[$smtpSettings]) ? $this->emailSettings[$smtpSettings] : [];

    // \Drupal::messenger()->addMessage($smtpSettings .'=>'.print_r($userInputs, 1));

    $form['container'] = [
        '#prefix'=>'<div id="container">',
        '#suffix'=>'</div>'
    ];

    $form['container']['debug_type'] = [
        '#type' => 'select',
        '#title' => $this->t('Debug Type'),
        '#options'=>[
            SMTP::DEBUG_OFF =>'Off',
            SMTP::DEBUG_CLIENT =>'Client Messages',
            SMTP::DEBUG_SERVER =>'Client and Server Messages']
    ];

    $form['container']['username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your username'),
      '#ajax'=> $ajaxSettings
    ];

    $form['container']['password'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Your password'),
    ];

    $form['container']['smtp_settings'] = [
        '#type' => 'select',
        '#title' => $this->t('Email Providers'),
        '#options'=>[
            'gmail'=>'Gmail',
            'yahoo'=>'Yahoo',
            'yandex'=>'Yandex',
            'aol'=>'AOL',
            'hotmail'=>'Hotmail',
            'outlook'=>'Outlook.com',
            'msn'=>'MSN',
            'rediff'=>'Rediff Mail'],
        '#ajax'=> $ajaxSettings
    ];

    $form['container']['smtp_server'] = [
        '#type' => 'textfield',
        '#title' => $this->t('SMTP Server'),
        '#default_value' => !empty($defaultValues['smtp_server']) ? $defaultValues['smtp_server'] : ''
    ];

    $form['container']['port'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Port'),
        '#default_value' => !empty($defaultValues['port']) ? $defaultValues['port'] : ''
    ];

    $form['container']['encryption'] = [
        '#type'=>'checkbox',
        '#title'=>'Enable Encryption?',
        '#return_value'=>'true'
    ];

    $form['container']['protocol'] = [
        '#type' => 'select',
        '#title' => $this->t('Protocol'),
        '#options'=>[
            PHPMailer::ENCRYPTION_STARTTLS =>'TLS',
            PHPMailer::ENCRYPTION_SMTPS =>'SSL',
            false => 'No Encryption'],
        '#default_value' => !empty($defaultValues['protocol']) ? $defaultValues['protocol'] : ''
    ];

    $form['container']['from_email'] = [
        '#type' => 'textfield',
        '#title' => $this->t('From Email Address'),
        
    ];

    $form['container']['reply_to'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Reply to Address'),
        
    ];

    $form['container']['to_email'] = [
        '#type' => 'textfield',
        '#title' => $this->t('To Email Address'),
        '#default_value'=>'tomc7898@gmail.com'
    ];

    $form['container']['subject'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Subject'),
        '#default_value'=>'This is a Test Subject'
    ];

    $form['container']['body'] = [
        '#type' => 'textarea',
        '#title' => $this->t('Body'),
        '#default_value'=>'This is a Test Body'
    ];
    
    $form['container']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    ];
    return $form;
  }

  public function myAjaxCallback(array &$form, FormStateInterface $form_state) {

        // $form['container']['smtp_server']['#value'] = '';
        $smtpSettings = $form_state->getValue('smtp_settings');
        $defaultValues = !empty($this->emailSettings[$smtpSettings]) ? $this->emailSettings[$smtpSettings] : [];
        $form['container']['smtp_server']['#value'] = !empty($defaultValues['smtp_server']) ? $defaultValues['smtp_server'] : '';
        $form['container']['protocol']['#value'] = !empty($defaultValues['protocol']) ? $defaultValues['protocol'] : false;
        $form['container']['port']['#value'] = !empty($defaultValues['port']) ? $defaultValues['port'] : '';
        $form['container']['encryption']['#checked'] = !empty($defaultValues['encryption']) && $defaultValues['encryption'] == 'false' ? false : true;
        $username = $form_state->getValue('username');
        $form['container']['from_email']['#value'] = !empty($username) ? $username : '';
        $form['container']['reply_to']['#value'] = !empty($username) ? $username : '';
        return $form['container'];
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $valid_email_regex = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,10})$^";
    $fields = ['username','password','smtp_server','port','to_email','subject','body'];
    $values = $form_state->getValues();
    foreach($fields as $fieldKey){
        if(empty($values[$fieldKey])){
            $form_state->setErrorByName($fieldKey, $this->t('The '.$fieldKey.' field is empty.'));
        }
    }

    if(!preg_match($valid_email_regex, $values['username'])){
        if(!preg_match($valid_email_regex, $values['from_email'])){
            $form_state->setErrorByName($fieldKey, $this->t('Please enter valid from email address.'));
        }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    // \Drupal::messenger()->addMessage(print_r($values,1));

    $mail = new PHPMailer;
    $mail->isSMTP();
    // SMTP::DEBUG_OFF = off (for production use)
    // SMTP::DEBUG_CLIENT = client messages
    // SMTP::DEBUG_SERVER = client and server messages
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->Host = $values['smtp_server'];
    $mail->Port = $values['port'];

    if(!$values['encryption']){
        $mail->SMTPAutoTLS = false;
        $mail->SMTPSecure = false;
    } else {
        $mail->SMTPSecure = $values['protocol'];
    }
    // $mail->SMTPKeepAlive = true;
    $mail->SMTPAuth = true;

    $mail->Username = $values['username'];
    $mail->Password = $values['password'];

    $mail->setFrom($values['from_email']);
    $mail->addReplyTo($values['reply_to']);
    $mail->addAddress($values['to_email']);
    $mail->Subject = $values['subject'];
    $mail->Body = $values['body'];

    if (!$mail->send()) {
        \Drupal::messenger()->addError($mail->ErrorInfo);
    } else {
        \Drupal::messenger()->addMessage('Mail Sent Successfully');
    }
    $form_state->setRebuild(true);
    // $mail->send();
    // $this->messenger()->addStatus($this->t('Your phone number is @number', ['@number' => $form_state->getValue('phone_number')]));
  }

}