<?php

namespace Drupal\controlpanel\Controller;

use Drupal\Core\Controller\ControllerBase;

use Ddeboer\Imap\Server;
use Ddeboer\Imap\Search\Date\Since;
use Ddeboer\Imap\SearchExpression;
use Ddeboer\Imap\Search\Flag;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

use Faker;

use Drupal\controlpanel\Controller\CPController;

class EmailReader extends CPController{

    public function fetchEmail($username, $password, $provider='gmail.com'){
        $providerSettings = [
            'gmail.com'=>[
                'host'=>'imap.gmail.com',
                'port'=>993
            ],
            'yahoo.com'=>[
                'host'=>'imap.mail.yahoo.com',
                'port'=>993
            ],
            'uscrm.email'=>[
                'host'=>'imap.uscrm.email',
                'port'=>143
            ],
            'itsol.space'=>[
                'host'=>'imap.itsol.space',
                'port'=>143
            ],
            'iserv.space'=>[
                'host'=>'imap.iserv.space',
                'port'=>143
            ]
        ];
        
        $providerSetting = $providerSettings[$provider];

        if($providerSetting['port'] == 993){
            $server = new Server($providerSetting['host'],$providerSetting['port'],'/imap/ssl/novalidate-cert');
        } else {
            $server = new Server($providerSetting['host'],$providerSetting['port'],'');
        }
        
        // $connection is instance of \Ddeboer\Imap\Connection
        // $connection = $server->authenticate( "wardanna938@yahoo.com", "qkgy livi fzrq lqyh");
        try {
            $connection = $server->authenticate( $username, $password);
            if(!empty($connection)){
                $mailboxes = $connection->getMailboxes();
                $mailbox = $connection->getMailbox('INBOX');

                $today = new \DateTimeImmutable();
                $thirtyDaysAgo = $today->sub(new \DateInterval('P15D'));

                $search = new SearchExpression();
                $search->addCondition(new Flag\Unseen());
                $search->addCondition(new Since($thirtyDaysAgo));

                $messages = $mailbox->getMessages($search,
                \SORTDATE, // Sort criteria
                true);

                $messagesFormatted = [];
                foreach ($messages as $message) {
                    try {
                    // $message is instance of \Ddeboer\Imap\Message
                        $toAddresses = $message->getTo();
                        $toAddressList = [];
                        $toAddressName = [];
                        foreach($toAddresses as $toAddress){
                            $toAddressList[] = $toAddress->getAddress();
                            $toAddressName[] = $toAddress->getName();
                        }
                        $fields = [
                            'account_message_id'=>$message->getId(),
                            'account_message_no'=>$message->getNumber(),
                            'subject'=>$message->getSubject(),
                            'fromEmail'=>$message->getFrom()->getAddress(),
                            'fromName'=>$message->getFrom()->getName(),
                            'bodyHTML'=>$message->getBodyHtml(),
                            'bodyText'=>$message->getBodyText(),
                            'toEmail'=>implode(',',$toAddressList),
                            'toName'=>implode(',',$toAddressName),
                            'timestamp'=> \DateTime::createFromImmutable($message->getDate())->format('Y-m-d H:i:s'),
                            'answered'=>empty($message->isAnswered()) ? 0 : 1
                        ];
                        $messagesFormatted[] = $fields;
                        $dbConnection = \Drupal::database();
                        $dbConnection
                            ->merge('mc__email_replies')
                            ->keys([
                                    'account_message_id'=>$message->getId(),
                                    'account_message_no'=>$message->getNumber(),
                                    'account_username'=>$username,
                                    'account_hostname'=>$provider])
                            ->fields($fields)
                            ->execute();
                    } catch (\Exception $em){
                        $this->setLog($em->getMessage());
                    }
                    $message->markAsSeen();
                }
                $activeFlag = 1;
            } else {
                $activeFlag = 0;
            }
        } catch (\Exception $e){
            $activeFlag = 0;
            $this->setLog($e->getMessage());
        }
        
        $dbConnection = \Drupal::database();
        $dbConnection
            ->merge('mc__imap_access')
            ->keys(['username'=>$username, 'provider'=>$provider])
            ->fields(['last_access'=>date("Y-m-d H:i:s"),'active'=>$activeFlag])
            ->execute();
    }

    public function imapEmailReader(){
        \Drupal::service('page_cache_kill_switch')->trigger();

        $form['file_modified'] = ['#markup' =>
        '<div>Last Modified - '.gmdate("Y-m-d\TH:i:s\Z", filemtime(__FILE__)).'</div>'];

        $dbConnection = \Drupal::database();
        $accounts = $dbConnection->query('SELECT * FROM `mc__imap_access` WHERE active = 1 and (last_access is null or TIMESTAMPDIFF(MINUTE,last_access,NOW()) > 10) order by last_access asc')->fetchAll();

        \Drupal::logger('imapmailer')->notice('Imap Mail Reader Script Running');

        foreach($accounts as $account){
            $this->fetchEmail($account->username, $account->password, $account->provider);
        }
        // $this->fetchEmail('cs@att-eservice.com', 'kolkata124@');
        // $this->fetchEmail('billing@att-eservice.com', 'kolkata123@');
        // $mbox = imap_open("{imap.gmail.com:993/imap/ssl/novalidate-cert}", 'cs@att-eservice.com', 'kolkata124@');
        // // $mbox = imap_open("{imap.mail.yahoo.com:993/imap/ssl/novalidate-cert}INBOX", "wardanna938@yahoo.com", "qkgy livi fzrq lqyh");
        // $date = date ( "d M Y", strToTime ( "-1 days" ) );
        // $search_criteria = "SINCE \"$date\""." UNSEEN";

        // $messages = imap_search( $mbox, $search_criteria);
        // $form['messagex'] = ['#markup'=>'PHP Imap => '.print_r($messages,1)];

        // $server = new Server('imap.mail.yahoo.com',993,'/imap/ssl/novalidate-cert');
        // $d1=new DateTime("2012-07-08 11:14:15.638276");
        // $d2=new DateTime("2012-07-08 11:14:15.889342"); 

        // $form['messagex2'] = ['#markup'=>'PHP Imap => '.print_r($messagesFormatted,1)];
        $form['html'] = ['#type'=>'textfield','#title'=>time()];

        return $form;
    }

    public function imapReply(){
        // \Drupal::messenger()->addMessage('Imap Mail Sender Running');
        \Drupal::logger('imapmailer')->notice('Imap Mail Sender Script Running');
        $faker = Faker\Factory::create();
        $dbConnection = \Drupal::database();
        $dbConnection->query('UPDATE `mc__email_replies` join mc__outbound_restrict_email on fromEmail = emailid SET `answered` = 1');
        $form['message'] = [];
        $messages = $dbConnection->query('select * from mc__email_replies where answered = 0 limit 20')->fetchAll();
        // \Drupal::messenger()->addMessage('=>'.print_r($messages,1));
        if(count($messages) > 0){
            // $mail = new PHPMailer;
            // $mail->isSMTP();
            // // SMTP::DEBUG_OFF = off (for production use)
            // // SMTP::DEBUG_CLIENT = client messages
            // // SMTP::DEBUG_SERVER = client and server messages
            // $mail->SMTPDebug = SMTP::DEBUG_OFF;
            // $mail->Host = 'smtp.gmail.com';
            // $mail->Port = 587;
            // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            // $mail->SMTPAuth = true;

            // $mail->Username = 'cs@att-eservice.com';
            // $mail->Password = 'kolkata124@';
            // $fromName = $faker->name;
            // $toName = ucwords(strtolower($message->fromName));
            // $mail->setFrom('cs@att-eservice.com',$fromName);
            // foreach($messages as $message){
                
            //     $body = '<p>Hi '.$toName .',</p>'.
            //     '<p>If you have any questions about your order please call <b>+1 814 631 5005</b> or
            //      visit our support section - https://att-eservice.com/support and fill the callback request form.</p>'.
            //      '<p>We will need your recorded voice authorization to cancel or modify this order. 
            //      <b>Cancellation/Modification via Email is currently unavailable.</b></p>'.
            //      '<p>Important : Due to developments related to COVID-19, some of our support centres are currently unavailable. Our teams are working to respond 
            //      to all incoming requests as soon as possible. We apologise for any inconvenience and appreciate your patience.</p>'.
            //      '<p>'.$fromName.'| Executive - Web Mail Center (WMC) | NL Services LLC<br>
            //      billing@att-eservice.com<br>NL Services LLC 1591 Ocean Ave, Santa Monica, CA 90401
            //      </p>'.
            //      '<p> This email was intended for '.$toName.' ('.$message->fromEmail.').</p>';
            //     $mail->addAddress($message->fromEmail, $toName);
            //     $mail->Subject = 'Re: '.$message->subject;
            //     $mail->msgHTML($body);
        
            //     if (!$mail->send()) {
            //         \Drupal::messenger()->addError($message->fromEmail.' => '.$mail->ErrorInfo);
            //         $form['errors'][] = ['#markup'=>$message->fromEmail.' => '.$mail->ErrorInfo.'<br>'];
            //     } else {
            //         $form['errors'][] = ['#markup'=>$message->fromEmail.' => '.'Mail Sent Successfully<br>'];
            //         \Drupal::messenger()->addMessage($message->fromEmail.' => '.'Mail Sent Successfully');
            //         $dbConnection
            //         ->merge('mc__email_replies')
            //         ->keys(['account_message_id'=>$message->account_message_id,
            //                 'account_message_no'=>$message->account_message_no,
            //                 'account_username'=>$message->account_username,
            //                 'account_hostname'=>$message->account_hostname])
            //         ->fields(['answered'=>1])
            //         ->execute();
            //     }
            //     $mail->clearAddresses();
            //     $mail->clearAttachments();
            // }
        }
        


        $form['html'] = ['#type'=>'textfield','#title'=>time()];

        return $form;
    }

}