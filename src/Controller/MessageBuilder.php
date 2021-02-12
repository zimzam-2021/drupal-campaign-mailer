<?php

namespace Drupal\controlpanel\Controller;

use Drupal\Core\Controller\ControllerBase;
use Faker;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

use Drupal\controlpanel\API\Email\EmailLogYahoo;

class MessageBuilder extends ControllerBase
{

    
    public function messageBuilder(){
        \Drupal::messenger()->addMessage('=>'.'HI');
        $form['hidden_input'] = ['#type'=>'hidden','value'=>'cdr'];
        $form['file_modified'] = ['#markup' =>'<div>Last Modified - '.gmdate("Y-m-d\TH:i:s\Z", filemtime(__FILE__)).'</div>'];
        // $form['subject'] = ['#markup' =>$htmlTable];
        return $form;
    }
}