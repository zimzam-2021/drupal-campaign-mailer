<?php

namespace Drupal\controlpanel\Form\Campaign;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class TimeSettings extends FormBase
{
  public function getFormId()
  {
    return 'timesettings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $database = \Drupal::database();
    $query = $database->query("SELECT * FROM campaign left join campaign__time using (campaign_id) order by campaign__time.active desc");
    $results = $query->fetchAll();
    //

    $did_options = $database->query("SELECT did_no, did_no FROM `mc__did_accounts`")->fetchAllKeyed(0, 0);

    $header = [
      'campaign_name' => 'Campaign Name',
      'did_no' => 'Timeline',
      'sitename' => 'Site Name'
    ];

    $options = [];
    $default_value = [];
    foreach ($results as $result) {
      $options[$result->campaign_id] = [
        'campaign_name' =>
        ['data' => [
          '#markup' => $result->campaign_name . '<br>' . $result->sitename . '<br>' . $result->sitename . '<br>' . $result->siteemail . '<br>' . $result->customercare_no,
        ]],
        'did_no' => array(
          'data' => array(
            '#type' => 'select',
            '#options' => $did_options,
            '#default_value' => json_decode($result->customercare_no)[0]
          )
        ),
      ];
      \Drupal::messenger()->addMessage(print_r(json_decode($result->customercare_no)[0],1));
      $default_value[$result->campaign_id] = $result->active;
    }

    $form['table'] = array(
      '#type' => 'tableselect',
      '#header' => $header,
      '#options' => $options,
      '#default_value' => $default_value,
      '#empty' => $this
        ->t('No shapes found'),
    );

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state)
  {
  }
}
