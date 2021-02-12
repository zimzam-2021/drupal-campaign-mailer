<?php

/**
 * @file
 * Contains Home Controller
 */

namespace Drupal\controlpanel\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Drupal\controlpanel\API\Email\SMTP_validateEmail;
use Drupal\controlpanel\API\Email\EmailTracker;
use Drupal\controlpanel\API\Email\Emailer;
use Drupal\controlpanel\API\Email\EmailLog;

use Drupal\controlpanel\API\MessageGenerator\MessageParameterGenertor;
use Drupal\controlpanel\API\MessageGenerator\NortonMessageGenerator;

use Drupal\controlpanel\API\Campaign\CampaignMailerNorton;
use Drupal\controlpanel\API\Campaign\CampaignMailerPCWorld;
use Drupal\controlpanel\API\Campaign\CampaignMailerPCWorld2;
use Drupal\controlpanel\API\Campaign\CampaignMailer;
use Drupal\controlpanel\API\Campaign\CampaignMailerYahoo;
use Drupal\controlpanel\API\Campaign\CampaignMailerYahoo2;
use Drupal\controlpanel\API\Campaign\CampaignMailerPCWorldNorton;
use Drupal\controlpanel\API\Campaign\CampaignATTMoney;
use Drupal\controlpanel\API\Charts\PHPCharts;

use Faker;

use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Response;

class Home extends ControllerBase
{
    public function homeRouter()
    {
        $dbConnection = \Drupal::database();
        $header = array(
            array('data' => t('Email ID'), 'field' => 'emailid'),
            array('data' => t('Total Count'), 'field' => 'total_count'),
            array('data' => t('Active Count'), 'field' => 'active_count'),
            array('data' => t('Active Mail Limit'), 'field' => 'available_mail_limit')
        );

        $query = $dbConnection->query('SELECT emailid , count(*) as total_count, sum(active) as active_count, SUM(case when active = 1 then max_email_count else 0 end) as available_mail_limit from mx_accounts group by emailid');
        $result = $query->fetchAllAssoc('emailid');

        $rows = [];
        foreach($result as $row) {
            $rows[] = array('data' => (array) $row);
        }

        $build['modified_date'] = ['#markup'=>'<div>Last Updated - '.filemtime(dirname(__FILE__).'/'.basename(__FILE__)).'</div>'];
    
        $build['mx_accounts'] = array(
            '#markup' => t('MX Account Status')
        );
        $build['mx_accounts']['location_table'] = array(
            '#theme' => 'table',
            '#header' => $header,
            '#rows' => $rows
        );

        $header = array(
            array('data' => t('Date'), 'field' => 'date'),
            array('data' => t('From Email'), 'field' => 'from_user'),
            array('data' => t('View Count'), 'field' => 'count'),
        );

        $query = $dbConnection->query('SELECT DATE(`timelog`) as date, from_user, count(DISTINCT to_user) as count FROM `email_view_log` where timelog >= DATE_ADD(CURDATE(), INTERVAL -2 DAY) GROUP by DATE(`timelog`), from_user order by date desc, count desc');
        $result = $query->fetchAll();
        $result = json_decode(json_encode($result),1);
        // \Drupal::messenger()->addMessage(print_r($result,1));
        $rows = [];
        foreach($result as $row) {
            $rows[] = array('data' => (array) $row);
        }
    
        $build['email_log'] = array(
            '#markup' => t('Email View Account Status')
        );
        $build['email_log']['location_table'] = array(
            '#theme' => 'table',
            '#header' => $header,
            '#rows' => $rows
        );

        $header = array(
            array('data' => t('Date'), 'field' => 'date'),
            array('data' => t('Campaign Nickname'), 'field' => 'campaign_name'),
            array('data' => t('Website/Product Name'), 'field' => 'sitename'),
            array('data' => t('Total Lead Batch'), 'field' => 'count'),
            array('data' => t('Sent Email Count'), 'field' => 'mail_sent'),
        );

        $query = $dbConnection->query('select email_sent_log.date, c.campaign_name, c.sitename, email_sent_log.count, email_sent_log.mail_sent from (SELECT campaign_id, date(timestamp_log) as date, count(*) as count, sum(mail_sent) as mail_sent FROM `leadcampaign` group by `campaign_id`, date(timestamp_log) order by date desc) email_sent_log left join campaign c using(campaign_id) limit 10');
        $result = $query->fetchAll();
        $result = json_decode(json_encode($result),1);
        // \Drupal::messenger()->addMessage(print_r($result,1));
        $rows = [];
        foreach($result as $row) {
            $data = (array) $row;

            $now = new \DateTime();
            $begin = new \DateTime('17:30');
            $end = new \DateTime('21:30');
    
            $special_begin = new \DateTime('9:30');
            $special_end = new \DateTime('13:30');
    
            $pre_special_begin = new \DateTime('13:30');
            $pre_special_end = new \DateTime('17:30');
    
    
            // if ($now >= $begin && $now <= $end){
            //     $data['mail_sent'] = round(intval($data['count'])*.6,0);
            // }
            // else if ($now >= $special_begin && $now <= $special_end){
            //     $data['mail_sent'] = round(intval($data['count'])*.3,0);
            // }
            // else if ($now >= $begin && $now <= $end){
            //     $data['mail_sent'] = round(intval($data['count'])*.9,0);
            // }

            // $data['mail_sent'] = $data['mail_sent']*2;
            $rows[] = array('data' => $data);
        }
    
        $build['email_sent_log'] = array(
            '#markup' => t('Email Sent Status')
        );
        $build['email_sent_log']['location_table'] = array(
            '#theme' => 'table',
            '#header' => $header,
            '#rows' => $rows
        );

        //SELECT count(DISTINCT to_user), DATE(timelog) as dates, HOUR(timelog) as hours, IF(MINUTE(timelog) < 30, 0, 30) as minutes FROM `email_view_log` WHERE timelog >= DATE_ADD(CURDATE(), INTERVAL -3 DAY) group by dates, hours, minutes order by dates desc, hours asc, minutes asc

        // $sql = 'SELECT DATE(timelog) as dates, HOUR(timelog) as hours, count(DISTINCT to_user) as view_count FROM `email_view_log` WHERE timelog >= DATE_ADD(CURDATE(), INTERVAL -7 DAY) group by dates, hours order by dates desc, hours asc';
        // $query = $dbConnection->query($sql);
        // $result = $query->fetchAll();

        // $hours = array_fill(0,24,0);
        // $datasets = [];

        // foreach($result as $views){
        //     if(empty($datasets[$views->dates])){
        //         $datasets[$views->dates] = $hours;
        //     }
        //     $datasets[$views->dates][$views->hours] = $views->view_count;
        // }

        // // $build['charts_result'] = ['#markup'=>print_r($result, 1)];
        // // $build['charts_rd'] = ['#markup'=>print_r($datasets, 1)];
        // $barChart = new PHPCharts('line', 'user_view_timeline', null, ['#FF5733','#884EA0','#3498DB','#16A085',
        // '#27AE60','#F1C40F','#D35400','#283747',
        // '#FF5733','#884EA0','#3498DB','#16A085',
        // '#27AE60','#F1C40F','#D35400','#283747',
        // '#FF5733','#884EA0','#3498DB','#16A085',
        // '#27AE60','#F1C40F','#D35400','#283747']);
        // $barChart->set('data', $datasets);
        // $barChart->set('legend', ['17:00','18:00','19:00',
        // '20:00','21:00','22:00','23:00','00:00','01:00','02:00','03:00',
        // '04:00','05:00','06:00','07:00',
        // '08:00','09:00','10:00','11:00',
        // '12:00','13:00','14:00','15:00',
        // '16:00']);
        // // We don't to use the x-axis for the legend so we specify the name of each dataset
        // // $barChart->set('legendData', array_keys($datasets));
        // // $barChart->set('displayLegend', true);

        // $build['charts']['email_view_count'] =  array(
        //     '#type' => 'inline_template',
        //     '#template' => $barChart->returnFullHTML(),
        //     '#context' => [],
        // );
        

        $sql = 'SELECT DATE(timelog) as dates, HOUR(timelog) as hours, sum(mail_sent) as mail_sent_count from email_message WHERE timelog >= DATE_ADD(CURDATE(), INTERVAL -7 DAY) group by dates, hours order by dates desc, hours asc';
        $query = $dbConnection->query($sql);
        $result = $query->fetchAll();

        $hours = array_fill(0,24,0);
        $datasets = [];

        foreach($result as $views){
            if(empty($datasets[$views->dates])){
                $datasets[$views->dates] = $hours;
            }
            $datasets[$views->dates][$views->hours] = $views->mail_sent_count;
        }

        // $build['charts_result'] = ['#markup'=>print_r($result, 1)];
        // $build['charts_rd'] = ['#markup'=>print_r($datasets, 1)];
        $barChart2 = new PHPCharts('line', 'mail_sent_timeline', null, ['#FF5733','#884EA0','#3498DB','#16A085',
        '#27AE60','#F1C40F','#D35400','#283747',
        '#FF5733','#884EA0','#3498DB','#16A085',
        '#27AE60','#F1C40F','#D35400','#283747',
        '#FF5733','#884EA0','#3498DB','#16A085',
        '#27AE60','#F1C40F','#D35400','#283747']);
        $barChart2->set('data', $datasets);
        $barChart2->set('legend', ['17:00','18:00','19:00',
        '20:00','21:00','22:00','23:00','00:00','01:00','02:00','03:00',
        '04:00','05:00','06:00','07:00',
        '08:00','09:00','10:00','11:00',
        '12:00','13:00','14:00','15:00',
        '16:00']);
        // We don't to use the x-axis for the legend so we specify the name of each dataset
        // $barChart->set('legendData', array_keys($datasets));
        // $barChart->set('displayLegend', true);

        $build['charts']['email_sent_count'] =  array(
            '#type' => 'inline_template',
            '#template' => $barChart2->returnFullHTML(),
            '#context' => [],
        );
        // ['#markup'=> 'Charts => '.$barChart->returnFullHTML()];

        return $build;
    }

    public function testGround()
    {
        $renderer = \Drupal::service('renderer'); 
        // $faker = Faker\Factory::create();  
        $body = '';
        $campaignMailer = new CampaignMailer(16);
        $campaignMailer->getEmailAccount();
        $campaignMailer->createSMTPConnection(true);
        $dummy = false;
        $body = $campaignMailer->generateMailBody('bob.bab.2021@gmail.com','Harry Smith',$dummy);
        // // $body = $campaignMailer->generateMailBody('harry.smithswiss@yahoo.com','Harry Smith',$dummy);
        // // $body = $campaignMailer->generateMailBody('harry.smith.swiss@gmail.com','Harry Smith',$dummy);
        // // $body = $campaignMailer->generateMailBody('molidada@mail.com','Harry Smith',$dummy);
        // // $body = $campaignMailer->generateMailBody('harry.smith.swiss@outlook.com','Harry Smith',$dummy);


        if(!$dummy){
            $body = $body->getBody();
        }
        // $messageGenerator = new MessageGeneratorPCWorld();
        // $codeDescriptions = [];
        // $csv = '';
        // for($i = 0 ; $i < 100; $i++){
        //     // $codeDescriptions[] = [
        //     //     'code'=>sprintf(
        //     //         '%04x%04x%04x',
        //     //         mt_rand(0, 0xffff),
        //     //         mt_rand(0, 0x0C2f) | 0x4000,
        //     //         mt_rand(0, 0x3fff) | 0x8000
        //     //     ),
        //     //     'description'=>$faker->realText(rand(200, 80), rand(1,5))
                
        //     // ];
        //     $csv .= sprintf(
        //         '%04x%04x%04x',
        //         mt_rand(0, 0xffff),
        //         mt_rand(0, 0x0C2f) | 0x4000,
        //         mt_rand(0, 0x3fff) | 0x8000
        //     ).",'".$faker->sentence(2,rand(3,8))."'\n\r";
            
        // }
        // return new JsonResponse([
        //     'data' => $codeDescriptions
        //   ]);

        //   return new Response($csv, 200, array());
        // $mbox = imap_open("{imap.mail.yahoo.com:993/imap/ssl/novalidate-cert}INBOX", "harry.smithswiss@yahoo.com", "wlbn peba eevu ghqx");
        // // $mbox = imap_open("{localhost:995/pop3/ssl/novalidate-cert}", "user_id", "password");
        // //imap_append() appends a string to a mailbox. In this example your SENT folder.
        // // Notice the 'r' format for the date function, which formats the date correctly for messaging.
        // imap_append($mbox, "{imap.mail.yahoo.com:993/imap/ssl/novalidate-cert}Sent",
        //     "From: harry.smithswiss@yahoo.com\r\n".
        //     "To: ".$to."\r\n".
        //     "Subject: ".$subject."\r\n".
        //     "Date: ".date("r", strtotime("now"))."\r\n".
        //     "\r\n".
        //     $body.
        //     "\r\n"
        //     );

        // // close mail connection.
        // imap_close($mbox);

        // $post = [
        //     'method' => 'getSynText',
        //     'text' => 'This email and any files transmitted with it are confidential and intended solely for the use of the individual or entity to whom they are addressed. If you have received this email in error please notify the system manager. This message contains confidential information and is intended only for the individual named. If you are not the named addressee you should not disseminate, distribute or copy this e-mail. Please notify the sender immediately by e-mail if you have received this e-mail by mistake and delete this e-mail from your system. If you are not the intended recipient you are notified that disclosing, copying, distributing or taking any action in reliance on the contents of this information is strictly prohibited.',
        //     //'text' => 'Important : Due to developments related to COVID-19, some of our support centres are currently unavailable. Our teams are working to respond to all incoming requests as soon as possible. We apologise for any inconvenience and appreciate your patience.',
        //     'backLight' => 0, // optional parameter, if passed, word highlighting will be enabled
        //     ];
        //     $ch = curl_init('https://rephrase-tool.com/api/index.php');
        //     curl_setopt($ch, CURLOPT_HTTPHEADER, [
        //     "X-Requested-With: XMLHttpRequest",
        //     ]);
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //     curl_setopt($ch, CURLOPT_POST, true);
        //     curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        //     $resutl_syn = curl_exec($ch);
        //     curl_close($ch);
        //     $syn = json_decode($resutl_syn, true);
        
            // server response
            
        $form['modified_date'] = filemtime(basename(__FILE__, '.php'));
        $form['email_stat'] = [ '#markup' => $dummy ? 'No Email will be Sent' : 'Email will be sent'];
        $form['email'] = array(
            '#type' => 'inline_template',
            '#template' => $body ,
            '#context' => [],
        );
        // $form['email_Edited'] = array(
        //     '#type' => 'inline_template',
        //     '#template' => print_r($syn,1) ,
        //     '#context' => [],
        // );

        return ['#markup' =>$renderer->render($form)];
    }


    public function cronEvery5Minutes()
    {
        // $this->emailAddressValidator(0);
        // \Drupal::logger('conrolpanel')->notice('Campaign Mailer Running');
        // $campaignMailer = new CampaignMailerPCWorld(6);
        // $campaignMailer->getEmailAccount();
        // $campaignMailer->createSMTPConnection();
        // \Drupal::messenger()->addMessage('Load Lead : Active Mail Sender !');
        // $campaignMailer->loadLead();
        return ['#markup' => 'Cron Running 5 Minutes '];
    }

    public function cronEvery10Minutes()
    {
        // $this->emailAddressValidator(1);
        // \Drupal::logger('conrolpanel')->notice('Campaign Mailer Running');
        // $campaignMailer = new CampaignMailerPCWorld(6);
        // $campaignMailer->getEmailAccount();
        // $campaignMailer->createSMTPConnection();
        // \Drupal::messenger()->addMessage('Load Lead : Active Mail Sender !');
        // $campaignMailer->loadLead();
        return ['#markup' => 'Cron Running 10 Minutes '];
    }

    public function cronEvery15Minutes()
    {
        // $this->emailAddressValidator(2);
        // \Drupal::logger('conrolpanel')->notice('Campaign Mailer Running');
        // $campaignMailer = new CampaignMailerPCWorld(6);
        // $campaignMailer->getEmailAccount();
        // $campaignMailer->createSMTPConnection();
        // \Drupal::messenger()->addMessage('Load Lead : Active Mail Sender !');
        // $campaignMailer->loadLead();
        return ['#markup' => 'Cron Running 15 Minutes '];
    }

    public function cronEvery30Minutes()
    {
        // $this->emailAddressValidator();
        // \Drupal::logger('conrolpanel')->notice('Campaign Mailer Running');
        // $campaignMailer = new CampaignMailerPCWorld(6);
        // $campaignMailer->getEmailAccount();
        // $campaignMailer->createSMTPConnection();
        // \Drupal::messenger()->addMessage('Load Lead : Active Mail Sender !');
        // $campaignMailer->loadLead();
        return ['#markup' => 'Cron Running 30 Minutes '];
    }

    public function emailCron()
    {
        $dbConnection = \Drupal::database();
        $dbConnection->query('SELECT * FROM `campaign` where active = 1')->fetchAll();
        \Drupal::messenger()->addMessage(print_r($values,1));
        if(date('D') == 'Sun') {
            \Drupal::logger('Mail Send API')->notice('Sunday => Mail Send API Not Running.');
        } else {
            $now = new \DateTime();
            $begin = new \DateTime('9:00');
            $end = new \DateTime('20:30');
    
            $special_begin = new \DateTime('13:00');
            $special_end = new \DateTime('16:00');
    
            $pre_special_begin = new \DateTime('12:30');
            $pre_special_end = new \DateTime('14:30');
    
    
            if ($now >= $begin && $now <= $end){
      
    
                if ($now >= $pre_special_begin && $now <= $pre_special_end){
                    \Drupal::logger('conrolpanel')->notice('Campaign Mailer Running => Special ATT EService');
                    $campaignMailer2 = new CampaignMailerPCWorld(11);
                    $campaignMailer2->getEmailAccount();
                    $campaignMailer2->createSMTPConnection();
                    \Drupal::messenger()->addMessage('Load Lead : Active Mail Sender Special 2 CampaignMailerPCWorld !');
                    $campaignMailer2->loadLead();
                }
    
                if ($now >= $special_begin && $now <= $special_end){
                    \Drupal::logger('conrolpanel')->notice('Campaign Mailer Running => Special ATT EService');
                    $campaignMailer2 = new CampaignMailerPCWorld(11);
                    $campaignMailer2->getEmailAccount();
                    $campaignMailer2->createSMTPConnection();
                    \Drupal::messenger()->addMessage('Load Lead : Active Mail Sender Special 2 CampaignMailerPCWorld !');
                    $campaignMailer2->loadLead();
                    $campaignMailer = new CampaignMailerYahoo(9);
                    $campaignMailer->getEmailAccount();
                    $campaignMailer->createSMTPConnection();
                    \Drupal::messenger()->addMessage('Load Lead : Active Mail Sender 9!');
                    $campaignMailer->loadLead();
                    \Drupal::logger('conrolpanel')->notice('Campaign Mailer Running !! Time Now => '.$now->format('H:i'));
                    \Drupal::logger('conrolpanel')->notice('Campaign Mailer Running => ATT EService');
                    $campaignMailer2 = new CampaignMailerPCWorld(10);
                    $campaignMailer2->getEmailAccount();
                    $campaignMailer2->createSMTPConnection();
                    \Drupal::messenger()->addMessage('Load Lead : Active Mail Sender 2 CampaignMailerPCWorld !');
                    $campaignMailer2->loadLead();
    
                }
    
                $campaignMailer = new CampaignMailerYahoo(9);
                $campaignMailer->getEmailAccount();
                $campaignMailer->createSMTPConnection();
                \Drupal::messenger()->addMessage('Load Lead : Active Mail Sender 9!');
                $campaignMailer->loadLead();
    
                \Drupal::logger('conrolpanel')->notice('Campaign Mailer Running => ATT EService 2 ');
                $campaignMailer2 = new CampaignMailerPCWorld(10);
                $campaignMailer2->getEmailAccount();
                $campaignMailer2->createSMTPConnection();
                \Drupal::messenger()->addMessage('Load Lead : Active Mail Sender 3 CampaignMailerPCWorld !');
                $campaignMailer2->loadLead();
    
                
                \Drupal::logger('conrolpanel')->notice('Campaign Mailer Running => ATT EService 3 ');
                $campaignMailer2 = new CampaignMailerPCWorld(11);
                $campaignMailer2->getEmailAccount();
                $campaignMailer2->createSMTPConnection();
                \Drupal::messenger()->addMessage('Load Lead : Active Mail Sender 3 CampaignMailerPCWorld 2!');
                $campaignMailer2->loadLead();
            } else {
                \Drupal::logger('conrolpanel')->notice('Campaign Mailer Not Running !! Time Now => '.$now->format('H:i'));
            }
            // \Drupal::logger('conrolpanel')->notice('Cron Job is running.');
            // $this->leadFormatter();
            // \Drupal::logger('conrolpanel')->notice('Campaign Mailer Running => PC World');
            // $campaignMailer = new CampaignMailerPCWorld(7);
            // $campaignMailer->getEmailAccount();
            // $campaignMailer->createSMTPConnection();
            // \Drupal::messenger()->addMessage('Load Lead : Active Mail Sender !');
            // $campaignMailer->loadLead();
    
            //SELECT * from (select username,GROUP_CONCAT(emailid) FROM `mx_accounts` where username like '%@gmail.com%' GROUP by `username`) a
    //left join (SELECT username from mx_accounts where emailid = 'transaction-alert@att-eservice.com') b on a.username = b.username where b.username is null
    
            
    
            // \Drupal::logger('conrolpanel')->notice('Campaign Mailer Running => P-CWorld');
            // $campaignMailer2 = new CampaignMailerPCWorldNorton(8);
            // $campaignMailer2->getEmailAccount();
            // $campaignMailer2->createSMTPConnection();
            // \Drupal::messenger()->addMessage('Load Lead : Active Mail Sender 3 !');
            // $campaignMailer2->loadLead();
        }
        
        return ['#markup' => 'Email Running'];
    }

    public function imagePixie($image)
    {
        \Drupal::logger('controlpanel')->notice('Pixie - ' . $image);
        $tracker = new EmailTracker();
        $tracker->setImageId($image);
        return new Response(
            $tracker->serveImage(),
            Response::HTTP_OK,
            ['content-type' => 'image/png']
        );
    }

    public function leadFormatter()
    {
        \Drupal::messenger()->addMessage('Lead Formatter is Running !!!');
        $connection = \Drupal::database();
        $query = 'select * from leads where username is null';
        $leadsList = $connection->queryRange($query, 0, 100)->fetchAll();
        $fields = [];
        foreach ($leadsList as $lead) {
            $email = $lead->email;
            $emailParts = explode("@", $email);
            if (count($emailParts) == 2) {
                $fields['username'] = $emailParts[0];
                $fields['domain'] = $emailParts[1];
            } else {
                $fields['user_exists'] = -1;
            }
            $connection->merge('leads')->key(array(
                'lead_id' => $lead->lead_id,
            ))
                ->fields($fields)
                ->execute();
        }
    }

    public function emailAddressValidator($count = 0)
    {
        \Drupal::messenger()->addMessage('Email Address Validator is Running !!!');
        $connection = \Drupal::database();
        $query = 'select * from leads where username is not null and cron_check_count = :count';
        $leadsList = $connection->queryRange($query, 0, 10, [':count' => $count])->fetchAll();
        $fieldsList = [];
        $emails = [];
        foreach ($leadsList as $lead) {
            $fieldsList[$lead->email] = [
                'lead_id' => $lead->lead_id,
                'user_exists' => $lead->user_exists,
                'cron_check_count' => $lead->cron_check_count
            ];
            array_push($emails, $lead->email);
        }
        $sender = 'postmaster@app.netflix-esolutions.com';
        $SMTP_Valid = new SMTP_validateEmail();
        $results = $SMTP_Valid->validate($emails, $sender);
        foreach ($results as $email => $status) {
            if (!empty($fieldsList[$email])) {
                $lead_id = $fieldsList[$email]['lead_id'];
                unset($fieldsList[$email]['lead_id']);
                $fields = $fieldsList[$email];
                if (empty($fields['user_exists'])) {
                    $fields['user_exists'] = 0;
                }
                $fields['user_exists'] += $status;
                $fields['cron_check_count']++;
                $connection->merge('leads')->key(array(
                    'lead_id' => $lead_id,
                ))
                    ->fields($fields)
                    ->execute();
            }
        }
    }



    public function getLeadsV2($campaign = '', $config = [])
    {
        global $base_url;
        $connection = \Drupal::database();
        $query = 'select l.*, ANY_VALUE(lc.campaign_id) from leads l left join leadcampaign lc using (lead_id) where campaign_id <> :campaign or campaign_id IS NULL and lead_id > 2401 and lead_id < 2802 group by lead_id';
        $leadsList = $connection->queryRange(
            $query,
            0,
            100,
            [':campaign' => 4]
        )->fetchAll();
        $leadsArray = json_decode(json_encode($leadsList), true);
        \Drupal::messenger()->addMessage('Response -' . print_r($leadsArray, 1));
        $leadsMailer = new LeadsMailer(4);
        foreach ($leadsArray as $lead) {
            $leadsMailer->setLeadInfo($lead);
            $leadsMailer->sendEmailWithAttachment();
        }
    }

    public function access(AccountInterface $account)
    {
        $session = \Drupal::request()->getSession();
    }

    public function accessTrue(AccountInterface $account)
    {
        return AccessResult::allowedIf(true);
    }
}

class LeadsMailer
{

    protected $dbConnection = [];
    protected $context = [];
    protected $leadContext = [];
    protected $accounts = [];
    protected $mailConfig = null;

    public function __construct($campaign_id)
    {
        global $base_url;
        $this->dbConnection = \Drupal::database();
        $this->campaignId = $campaign_id;
        $config = $this->dbConnection->query(
            'select * from campaign where campaign_id = :campaign_id',
            [':campaign_id' => $campaign_id]
        )->fetchAssoc();

        \Drupal::messenger()->addError('Config-' . print_r($config, 1));

        $mail = new PHPMailer;
        $mail->isSMTP();
        // SMTP::DEBUG_OFF = off (for production use)
        // SMTP::DEBUG_CLIENT = client messages
        // SMTP::DEBUG_SERVER = client and server messages
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        // $mail->Host = 'smtp.gmail.com';
        // $mail->Port = 587;

        // $mail->Host = 'smtp.office365.com';
        $mail->Host = 'smtp.office365.com';
        $mail->Port = 587;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->SMTPKeepAlive = true;
        $mail->SMTPAuth = true;

        $userNames = ['jordanbanshee78@outlook.com'];
        $currentUserNameIndexCache = \Drupal::cache()->get('currentUserNameIndex');

        $currentUserNameIndex = $currentUserNameIndexCache->data;
        \Drupal::logger('conrolpanel')->notice('Current Cache State - ' . $currentUserNameIndex);
        if (empty($current)) {
            $currentUserNameIndex = 0;
        }
        $emailAccountUserName = $userNames[$currentUserNameIndex];
        \Drupal::logger('conrolpanel')->notice('Current Cache UserName - ' . $currentUserNameIndex . '=>' . $emailAccountUserName);
        $currentUserNameIndex++;
        if ($currentUserNameIndex > 3) {
            $currentUserNameIndex = 0;
        }
        \Drupal::cache()->set('currentUserNameIndex', $currentUserNameIndex);
        \Drupal::logger('conrolpanel')->notice('Current Email Username - ' . $currentUserNameIndex . '=>' . $emailAccountUserName);
        $mail->Username = $emailAccountUserName;
        $mail->Password = 'kolkata123@';

        $this->mailConfig = $mail;




        $this->context = array(
            'campaignId' => $config['campaign_id'],
            'startLimit' => $config['startlimit'],
            'endLimit' => $config['endlimit'],
            'siteName' => $config['sitename'],
            'siteUrl' => $config['siteurl'],
            'logoUrl' => $config['logourl'],
            'siteEmail' => $config['siteemail'],
            'siteAddress' => $config['siteaddress'],
            'currency' => $config['currency'],
            'products' => json_decode($config['products'], 1),
            'productStartRange' => $config['product_start_range'],
            'productEndRange' => $config['product_end_range'],
            'customerCareNo' => $config['customercare_no'],
            'apiCredentials' => $config['api_credentials'],
            'logoUrl' => $config['logourl'],
            'startParagraph' => $this->safeRenderHTML($config['start_paragraph']),
            'endParagraph' => $this->safeRenderHTML($config['end_paragraph']),
            'startParagraphRaw' => $config['start_paragraph'],
            'endParagraphRaw' => $config['end_paragraph'],
            'callToActionLink' => $config['call_to_action_link'],
            'callToActionText' => $config['call_to_action_text'],
            'websiteURL' => $config['siteurl'],
            'websiteName' => $config['sitename'],
            'websiteAddress' => $config['siteaddress'],
            'cssTemplate' => $config['css_template'],
            'subjectTemplate' => $config['subject_template'],
            'attachmentFileNamePrefix' => $config['filename_prefix'] . '_CI' . $config['campaign_id'],
        );

        $this->accounts = [
            'username' => 'billing@att-eservice.com',
            'password' => 'kolkata123@'
        ];
    }

    public function safeRenderHTML($valueTemplate)
    {
        $renderer = \Drupal::service('renderer');
        return $form['footer'] = array(
            '#type' => 'inline_template',
            '#template' => $valueTemplate,
            '#context' => [],
        );
        $html = $renderer->render($form);
        return $html;
    }


    public function getContext()
    {
        return $this->context;
    }

    public function selectLeadById($lead_id)
    {
        $this->dbConnection = \Drupal::database();
        $lead = $this->dbConnection->query(
            'select * from leads where lead_id = :lead_id',
            [':lead_id' => $lead_id]
        )->fetchAssoc();
        $this->setLeadInfo($lead);
    }

    public function setLeadInfo($lead)
    {
        $this->leadContext = [];
        $context = $this->context;
        $this->leadId = $lead['lead_id'];
        $context['receiverName'] = ucwords(strtolower($lead['firstname'] . ' ' . $lead['lastname']));
        $context['receiverEmail'] = $lead['email'];
        $context['receiverAddress'] = ucwords(strtolower($lead['address'] . ', ' . $lead['county'] . ', ' . $lead['state'] . ', ' . $lead['zip']));
        $context['invoiceDate'] =  date('F d Y');
        $context['invoiceDueDate'] =  date('F d Y', strtotime(' +1 day'));
        $context['attachmentFileName'] = $context['attachmentFileNamePrefix'] . '_' . $lead['lead_id'];
        $context['attachmentFilePDFLocation'] = 'public://' . $context['attachmentFileName'] . '.pdf';
        $context['attachmentFileHTMLLocation'] = 'public://' . $context['attachmentFileName'] . '.html';
        $this->leadContext = $context;
    }

    public function createEmail()
    {
        $context = $this->leadContext;
        $context['emailBody'] = email_template(
            $context['siteUrl'],
            $context['siteName'],
            $context['logoUrl'],
            $context['subjectTemplate'] . ' (' . $context['receiverEmail'] . ')',
            $context['receiverName'],
            $context['receiverEmail'],
            $context['startParagraphRaw'],
            $context['callToActionLink'],
            $context['callToActionText'],
            $context['endParagraphRaw'],
            $context['websiteAddress']
        );

        //$this->emailBodyTemplate($context);
        $context['message'] = $this->createMessage(
            $context['receiverName'],
            $context['receiverEmail'],
            $context['siteName'],
            $context['siteEmail'],
            $context['subjectTemplate'] . ' (' . $context['receiverEmail'] . ')',
            $context['emailBody'],
            [$context['attachmentFilePDFLocation']]
        );


        $this->leadContext = $context;
    }




    public function createMessage($receiverName, $receiverEmail, $siteName, $siteEmail, $subject, $body, $attachments = [])
    {
        $message = [];
        $message['from'] = ['name' => $siteName, 'address' => $siteEmail];
        $message['to'] = ['name' => $receiverName, 'address' => $receiverEmail];
        $message['subject'] = $subject;
        $message['body'] = $body;
        $message['attachments'] = $attachments;
        return $message;
    }

    public function doMail($message, $account)
    {
        $mail = $this->mailConfig;
        if (!empty($mail)) {
            $mail->setFrom('jordanbanshee78@outlook.com', '24x7ITech');
            // $mail->addReplyTo('billing@att-eservice.com', $fromName);


            // $mail->setFrom($message['from']['address'],$message['from']['name']);
            // $mail->addReplyTo($message['from']['address'], $message['from']['name']);


            $mail->addAddress($message['to']['address'], $message['to']['name']);
            $mail->Subject = $message['subject'];
            $mail->msgHTML($message['body']);
            $mail->AltBody = $message['subject'];

            foreach ($message['attachments'] as $attachment) {
                $attachmentPath = \Drupal::service('file_system')->realpath($attachment);
                $mail->addAttachment($attachmentPath);
                \Drupal::messenger()->addMessage($attachmentPath);
            }
            //send the message, check for errors
            if (!$mail->send()) {
                \Drupal::messenger()->addMessage('Response -' . $mail->ErrorInfo);
                // return false;

            } else {
                $connection = \Drupal::database();
                $connection->merge('leadcampaign')->key(array(
                    'campaign_id' => $this->campaignId,
                    'lead_id' => $this->leadId,
                ))
                    ->fields(['mail_sent' => 1])
                    ->execute();
                \Drupal::messenger()->addMessage('Mail Sent');
                // return true;

            }
            \Drupal::logger('conrolpanel')->notice(
                'Mail Sent to ' . $message['to']['address'] . ' Error Status -' . $mail->ErrorInfo
            );
            $mail->clearAddresses();
            $mail->clearAttachments();
        }
    }

    public function sendEmailWithAttachment()
    {
        $this->createAttachmentIfNotGenerated();
        if ($this->isAttachmentGenerated()) {
            $this->createEmail();
            if (!empty($this->leadContext['message'])) {
                $this->doMail($this->leadContext['message'], $this->accounts);
            }
        }
    }

    public function createAttachment()
    {
        $context = $this->leadContext;
        if (!file_exists($context['attachmentFileHTMLLocation'])) {
            $htmlInvoice = invoice_template(
                $context['siteName'],
                $context['attachmentFileName'],
                $context['logoUrl'],
                $context['invoiceDate'],
                $context['invoiceDueDate'],
                $context['siteAddress'],
                $context['receiverName'],
                $context['receiverAddress'],
                $context['receiverEmail'],
                $context['currency'],
                $context['products'],
                $context['productStartRange'],
                $context['productEndRange']
            );
            \Drupal::service('file_system')->saveData($htmlInvoice, $context['attachmentFileHTMLLocation']);
        }
        $pdf = new \wPDF(
            DRUPAL_PUBLIC_PATH . $context['attachmentFileName'] . '.html',
            $context['attachmentFileName'],
            DRUPAL_PUBLIC_PATH
        );
        $pdf->binary(WKHTMLTOPDF_BINARY_COMMAND . " --page-size A5");
        $pdf->generatePDF();
    }

    public function createAttachmentIfNotGenerated()
    {
        if (!$this->isAttachmentGenerated()) {
            $this->createAttachment();
        }
    }

    public function isAttachmentGenerated()
    {
        $context = $this->leadContext;
        if (!file_exists($context['attachmentFilePDFLocation'])) {
            return false;
        } else {
            return true;
        }
    }

    public function emailBodyTemplate($context)
    {
        $renderer = \Drupal::service('renderer');
        $styles = file_get_contents(dirname(__DIR__, 1) . '/templates/css/template3.css');
        \Drupal::messenger()->addMessage('Style Found -> ' . dirname(__DIR__, 1) . '/templates/css/template3.css =>' . $styles);

        $form['head'] = $this->headTemplate($context);
        $form['body'] = [
            '#prefix' => '
                            <span class="preheader">' . $context['subject'] . '</span>
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
                                    <tbody><tr>
                                        <td>&nbsp;</td>
                                        <td class="container">
                                            <div class="content">',
            '#suffix' => '</div>
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                    </tbody></table>
                '
        ];
        $form['body']['content'] = $this->contentTemplate($context);
        $form['body']['footer'] = $this->footerTemplate($context);
        $html = '<html>' . $renderer->render($form['head']) . '<body>' . $renderer->render($form['body']) . '</body></html>';
        return $html;
    }


    public function contentTemplate($context)
    {
        $form['body'] = [];
        $form['body']['#prefix'] = '<table role="presentation" class="main"><tbody>';
        $form['body']['#suffix'] = '</tbody></table>';

        $form['body']['logo'] = array(
            '#prefix' => '<tr><td class="wrapper">',
            '#type' => 'inline_template',
            '#template' => '<table><tbody><tr><td><img src="{{logoUrl}}" width="150px"></td><td></td></tr></tbody></table>',
            '#context' => $context,
            '#suffix' => '</td></tr>'
        );

        $form['body']['content'] = array(
            '#prefix' => '<tr><td class="wrapper">',
            '#suffix' => '</td></tr>'
        );
        $form['body']['content']['template'] = $this->contentBodyTemplate($context);
        return $form;
    }

    public function headTemplate($context)
    {
        $context['style'] = file_get_contents(dirname(__DIR__, 1) . '/templates/css/' . $context['cssTemplate']);
        $template = '
            <head>
                <meta name="viewport" content="width=device-width">
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <title>{{subject}}</title>
                <style>
                    {{style}}
                </style>
            </head>
        ';
        return $form['head'] = array(
            '#type' => 'inline_template',
            '#template' => $template,
            '#context' => $context,
        );
    }

    public function footerTemplate($context)
    {
        $template = '
            <div class="footer">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tbody><tr>
                        <td class="content-block">
                            <a href="{{websiteURL}}/unsubscribe">Unsubscribe</a> |
                            <a href="{{websiteURL}}/help">Help</a>
                        </td>
                    </tr>
                    <tr>
                        <td class="content-block">
                            <span class="apple-link">You are receiving {{websiteName}} notification emails.</span>
                            <br> Don\'t like these emails?

                        </td>
                    </tr>
                    <tr>
                        <td class="content-block">
                            This email was intended for {{receiverName}} ({{receiverEmail}}). <br>
                            <a href="{{websiteURL}}/email-footer">Learn why we included this.</a>
                        </td>
                    </tr>
                    <tr>
                        <td class="content-block">
                            © 2019 {{websiteName}}, {{websiteAddress}}.
                            {{websiteName}} is a registered business name of {{websiteName}} LLC.
                            {{websiteName}} and the {{websiteName}} logo are registered trademarks of {{websiteName}} LLC.
                        </td>
                    </tr>
                </tbody></table>
            </div>';

        return $form['footer'] = array(
            '#type' => 'inline_template',
            '#template' => $template,
            '#context' => $context,
        );
    }

    public function contentBodyTemplate($context)
    {
        $template = '<table role="presentation" border="0" cellpadding="0" cellspacing="0">
            <tbody>
                <tr>
                    <td>
                        <p>Hi {{receiverName}},</p>
                        {{startParagraph}}
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                            <tbody>
                                <tr>
                                    <td align="left">
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                            <tbody>
                                                <tr><td><a href="{{callToActionLink}}" target="_blank">{{callToActionText}}</a></td></tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        {{endParagraph}}
                    </td>
                </tr>
            </tbody>
        </table>';

        return $form['body'] = array(
            '#type' => 'inline_template',
            '#template' => $template,
            '#context' => $context,
        );
    }
}
