<?php

namespace Drupal\controlpanel\Controller\Campaign\Email;

use Drupal\Core\Controller\ControllerBase;
use Drupal\controlpanel\Controller\CPController;
use Symfony\Component\HttpFoundation\JsonResponse;
// use Drupal\controlpanel\API\Campaign\CampaignMailerYahoo;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Faker;

/**
 * Implements an example form.
 * 
 * 
 */

class API extends CPController {

    public function unSubscribeEmail($domain, $emailId, $messageId = 0){
        $dbConnection = \Drupal::database();

        $status = $dbConnection->insert('mx__unsubscribe')
        ->fields([
          'domain' => $domain,
          'email_id' => $emailId,
          'message_id'=> $messageId
        ])
        ->execute();
        return new JsonResponse(['status'=>$status]);
    }

    public function CampaignEmail($campaignId){

    }

    public function CampaignEmailAPI($campaignId){

    }

    public function CampaignEmailAPITest($campaignId, $dummy = true){
        $renderer = \Drupal::service('renderer'); 
        $dbConnection = \Drupal::database();
        $campaign = $dbConnection->query('SELECT * FROM `campaign` c left join campaign__time ct using(campaign_id) where c.campaign_id = :campaign_id',
            [':campaign_id'=> $campaignId]
        )->fetch();
        $form['modified_date']['#markup'] = filemtime(basename(__FILE__, '.php'));

        if(!empty($campaign)){
            $emailerClass = $campaign->class;
            if(!empty($emailerClass)){
                try{
                    $emailerClass = "Drupal\controlpanel\API\Campaign\\$emailerClass";
                    $campaignMailer = new $emailerClass($campaign->campaign_id);

                    $body = '';
                    $campaignMailer->getEmailAccount();
                    $campaignMailer->createSMTPConnection(true);
                    $body = $campaignMailer->generateMailBody('bob.bab.2021@gmail.com','Bublai Chowdhury',$dummy);
                    // $body = $campaignMailer->generateMailBody('jennifferpackwood98@yahoo.com','Harry Smith',$dummy);
                    // $body = $campaignMailer->generateMailBody('dan247cra1@gmail.com','Harry Smith',$dummy);
                    // $body = $campaignMailer->generateMailBody('tomc7898@gmail.com','Harry Smith',$dummy);
   
                    if(!$dummy){
                        $body = $body->getBody();
                    }
                    $form['email_to_be'] = ['#markup'=>'bob.bab.2021@gmail.com'];
                    $form['email_stat'] = [ '#markup' => $dummy ? 'No Email will be Sent' : 'Email will be sent'];
                    $form['email'] = array(
                        '#type' => 'inline_template',
                        '#template' => $body ,
                        '#context' => [],
                    );

                } catch (\Exception $e){
                    $this->setLog($e->getMessage());
                }
            }
        }
        return ['#markup' =>$renderer->render($form)];
    }

    public function CampaignEmailAPICron(){
        $activeDays = constant(date('D'));
        $activeDays = !empty($activeDays) ? $activeDays : 0;
        $dbConnection = \Drupal::database();

        $this->setLog("Campaign Email API Cron is Running");

        $returnResponse = [];

        $activeCampaigns = $dbConnection->query('SELECT * FROM `campaign` c left join campaign__time ct using(campaign_id)
                where ct.active = 1 and activeDays & :active_days and TIME(NOW()) between ct.starttime and ct.endtime',
                [':active_days'=> $activeDays]
            )->fetchAll();

        $returnResponse['dayOfWeek'] = date('D');
        $returnResponse['activeDays'] = $activeDays;
        $returnResponse['activeCampaigns'] = $activeCampaigns;
        $returnResponse['campaignMailer'] = [];
        

        foreach($activeCampaigns as $campaign){
            $emailerClass = $campaign->class;
            if(!empty($emailerClass)){
                try{
                    $emailerClass = "Drupal\controlpanel\API\Campaign\\$emailerClass";
                    $campaignMailer = new $emailerClass($campaign->campaign_id);
                    $returnResponse['campaignMailer'][$campaign->campaign_id] = $campaignMailer;
                    $campaignMailer->getEmailAccount();
                    $campaignMailer->createSMTPConnection();
                    $this->setLog('Campaign Running => '.$campaign->campaign_id);
                    $campaignMailer->loadLead();
                } catch (\Exception $e){
                    $this->setLog($e->getMessage());
                }
            }
        }
        // \Drupal::messenger()->addMessage(print_r($results,1));
        return new JsonResponse($returnResponse);
    }

}
