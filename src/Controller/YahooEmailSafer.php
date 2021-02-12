<?php

namespace Drupal\controlpanel\Controller;

use Drupal\Core\Controller\ControllerBase;
use Faker;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

use Drupal\controlpanel\API\Email\EmailLogYahoo;

class YahooEmailSafer extends ControllerBase
{
    public function yahooSafeMessageGenerator(){
        $faker = Faker\Factory::create();

        $message = '';
        $value = rand(1,4);
        switch($value){
            case 1:
                $message .= '<div>'.$faker->text.'</div>';
                $message .= '<div>'.$faker->name.'</div>';
                $message .= '<div>'.$faker->address.'</div>';
                $message .= '<div>'.$faker->text.'</div>';
                break;
            case 2:
                $message .= '<div>Hi '.$faker->firstName().',</div>';
                $message .= '<div>'.$faker->realText().'</div>';
                $message .= '<div>'.$faker->realText(rand(200, 800), rand(1,5)).'</div>';
                break;
            case 3:
                $message .= '<div>'.$faker->company.', '.$faker->jobTitle.',</div>';
                $message .= '<div>'.$faker->text(rand(300,700)).'</div>';
                $message .= '<div><b>'.$faker->freeEmail.'</b></div>';
                $message .= '<div><b>'.$faker->userAgent.'</b></div>';
                break;
            case 4:
            default:
                $properties = ['name','freeEmail','company','text','address','sha256','text','companyEmail','timezone','text','phoneNumber'];
                $selected_properties = array_rand($properties,5);
                foreach($selected_properties as $selected_property){
                    $message .= '<div>'.$faker->{$properties[$selected_property]}.'</div>';
                }
                break;
        }
        return $message;
    }

    public function availableYahooEmailAccounts(){
        $dbConnection = \Drupal::database();
        // $query = 'SELECT ma.*
        // FROM mc__yahoo_mx_accounts ma
        // LEFT JOIN mc__yahoo_mx_log mal
        // ON ma.username = mal.username AND mal.date = CURRENT_DATE
        // WHERE active = 1
        //     AND (
        //         date IS NULL
        //         OR (
        //             mail_count < max_email_count
        //             AND (
        //                 test_mail_count >= 7 AND mail_count < 2 OR
        //                 test_mail_count >= 14 AND mail_count < 4 OR
        //                 test_mail_count >= 21 AND mail_count < 6 OR
        //                 test_mail_count >= 28 AND mail_count < 8 OR
        //                 test_mail_count >= 35 AND mail_count < 10 OR
        //                 test_mail_count >= 42 AND mail_count < 12 OR
        //                 test_mail_count >= 49 AND mail_count < 14 OR
        //                 test_mail_count >= 56 AND mail_count < 16 OR
        //                 test_mail_count >= 63 AND mail_count < 18 OR
        //                 test_mail_count >= 70 AND mail_count < 20
        //             )
        //             AND date = CURRENT_DATE
        //             AND TIMESTAMPDIFF(MINUTE,last_mail_sent,NOW()) > min_time_diff
        //         )
        //     )
        // ORDER BY last_mail_sent ASC LIMIT 1';
        $query = '
            SELECT *
            FROM mc__yahoo_mx_accounts ma
            LEFT JOIN mc__yahoo_mx_log mal
            ON ma.username = mal.username AND mal.date = CURRENT_DATE
            WHERE active = 1
                AND (
                    date IS NULL
                    OR (
                        mail_count < max_email_count
                        AND (test_mail_count BETWEEN 0 and 80 and mail_count BETWEEN 0 and 20)
                        AND date = CURRENT_DATE
                        AND TIMESTAMPDIFF(MINUTE,last_mail_sent,NOW()) > min_time_diff
                    )
                )  
            ORDER BY `mal`.`test_mail_count`  DESC, mal.last_mail_sent desc limit 1;
        ';
        $mailConfig = $dbConnection->query($query)->fetch();
        \Drupal::messenger()->addMessage('Mail Config =>'.print_r($mailConfig, 1));
        return ['#markup'=>''];
    }

    public function sendSafeMessageFromYahoo(){
        \Drupal::logger('yahoomailer')->notice('Yahoo Mailer Script Running');
        $this->sendSafeMessageFromYahooProcessor();
        $this->sendSafeMessageFromYahooProcessor();
        $this->sendSafeMessageFromYahooProcessor();
        $this->sendSafeMessageFromYahooProcessor();
        $this->sendSafeMessageFromYahooProcessor();
        $form['file_modified'] = ['#markup' =>'<div>Last Modified - '.gmdate("Y-m-d\TH:i:s\Z", filemtime(__FILE__)).'</div>'];

        return $form;
    }

    public function sendSafeMessageFromYahooProcessor(){
        $faker = Faker\Factory::create();
        $dbConnection = \Drupal::database();

        $query = 'SELECT ma.* FROM mc__yahoo_mx_accounts ma LEFT JOIN mc__yahoo_mx_log mal 
        ON ma.username = mal.username AND mal.date = CURRENT_DATE WHERE active = 1 AND 
        ( date IS NULL OR ( (mail_count = 0 and test_mail_count < 7 OR test_mail_count % mail_count < 6) 
        AND date = CURRENT_DATE AND TIMESTAMPDIFF(MINUTE,last_mail_sent,NOW()) > min_time_diff ) ) 
        ORDER BY last_mail_sent ASC LIMIT 1';

        \Drupal::logger('yahoomailer')->notice('Yahoo Mailer Processor Script Running');

        $mailConfig = $dbConnection->query($query)->fetch();
        \Drupal::messenger()->addMessage('Mail Config =>'.print_r($mailConfig, 1));
        if(!empty($mailConfig->username)){
            $mailLog = new EmailLogYahoo($mailConfig->username);
            $toEmailIds = $dbConnection->query('SELECT tma.* FROM mc__test_mx_accounts tma left 
            join mc__yahoo_test_account_log ytal on tma.test_email_id = ytal.test_email_id and yahoo_username = :email_id  
                    where yahoo_username = :email_id or yahoo_username is null 
                    order by count asc, last_mail_sent desc limit 2',[':email_id'=>$mailConfig->username])->fetchAll();

            $mail = new PHPMailer;
            $mail->isSMTP();
            // SMTP::DEBUG_OFF = off (for production use)
            // SMTP::DEBUG_CLIENT = client messages
            // SMTP::DEBUG_SERVER = client and server messages
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->Host = 'smtp.mail.yahoo.com';
            $mail->Port = 465;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->SMTPAuth = true;

            $mail->Username = $mailConfig->username;
            $mail->Password = $mailConfig->password;

            $mail->setFrom($mailConfig->username);

            \Drupal::messenger()->addMessage('Email Id => '.print_r($toEmailIds, 1));

            foreach($toEmailIds as $toEmailId){
                $mail->addAddress($toEmailId->test_email_id);
                $mail->Subject = $faker->realText(50,1);
                $mail->msgHTML($this->yahooSafeMessageGenerator());
        
                if (!$mail->send()) {
                    \Drupal::messenger()->addError($mail->ErrorInfo);
                    $mailLog->setInactive();
                } else {
                    \Drupal::messenger()->addMessage('Mail Sent Successfully');
                    $mailLog->setTestLog();
                    $mailLog->setTestEmailIdUsedLog($toEmailId->test_email_id);
                }
                $mail->clearAddresses();
                $mail->clearAttachments();
            }
        }
        $form['file_modified'] = ['#markup' =>'<div>Last Modified - '.gmdate("Y-m-d\TH:i:s\Z", filemtime(__FILE__)).'</div>'];
        $form['subject'] = ['#markup' => '<b>'.$faker->realText(50,1).'</b>'];
        $form['message'] = ['#markup' => $this->yahooSafeMessageGenerator()];

        return $form;
    }

    public function updateMailLog($username){
        $dbConnection = \Drupal::database();
        $transaction = $dbConnection->startTransaction();
        try{
            $log = $dbConnection->query(
                'select * from mc__yahoo_mx_log where username = :username and date = :date limit 1',
                [':username'=> $username, 'date' => date('Y-m-d')],
            )->fetch();
            $mail_count = 0;
            if(!empty($log) && !empty($log->username)){
                $mail_count = $log->mail_count;
            }
            $mail_count++;
            $dbConnection
                ->merge('mc__yahoo_mx_log')
                ->keys(['username'=>$username, 'date'=> date('Y-m-d')])
                ->fields(['mail_count'=>$mail_count,'last_mail_sent'=>date("Y-m-d H:i:s")])
                ->execute();
        } catch (\Exception $e) {
            \Drupal::messenger()->addMessage($e->getMessage());
            $transaction->rollBack();
        }
    }

    public function updateTestMailLog($username){
        $dbConnection = \Drupal::database();
        $transaction = $dbConnection->startTransaction();
        try{
            $log = $dbConnection->query(
                'select * from mc__yahoo_mx_log where username = :username and date = :date limit 1',
                [':username'=> $username, 'date' => date('Y-m-d')],
            )->fetch();
            $mail_count = 0;
            if(!empty($log) && !empty($log->username)){
                $mail_count = $log->test_mail_count;
            }
            $mail_count++;
            $dbConnection
                ->merge('mc__yahoo_mx_log')
                ->keys(['username'=>$username, 'date'=> date('Y-m-d')])
                ->fields(['test_mail_count'=>$mail_count,'last_mail_sent'=>date("Y-m-d H:i:s")])
                ->execute();
        } catch (\Exception $e) {
            \Drupal::messenger()->addMessage($e->getMessage());
            $transaction->rollBack();
        }
    }

    public function updateTestMailIdSentLog($username, $tomailid){
        $dbConnection = \Drupal::database();
        $transaction = $dbConnection->startTransaction();
        try{
            $log = $dbConnection->query(
                'select * from mc__yahoo_test_account_log where yahoo_username = :yahoo_username 
                and test_email_id = :test_email_id limit 1',
                [':yahoo_username'=> $username, 'test_email_id' => $tomailid],
            )->fetch();
            $mail_count = 0;
            if(!empty($log) && !empty($log->username)){
                $mail_count = $log->count;
            }
            $mail_count++;
            $dbConnection
                ->merge('mc__yahoo_test_account_log')
                ->keys(['yahoo_username'=>$username, 'test_email_id' => $tomailid])
                ->fields(['count'=>$mail_count,'last_mail_sent'=>date("Y-m-d H:i:s")])
                ->execute();
        } catch (\Exception $e) {
            \Drupal::messenger()->addMessage($e->getMessage());
            $transaction->rollBack();
        }
    }

    public function setInactive($username){
        \Drupal::database()
                ->merge('mc__yahoo_mx_accounts')
                ->keys(['username'=>$username])
                ->fields(['active' => 0])
                ->execute();
    }

}