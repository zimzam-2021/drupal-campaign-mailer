<?php

namespace Drupal\controlpanel\Controller;

use Drupal\Core\Controller\ControllerBase;

use Drupal\controlpanel\Controller\CPController;
use Drupal\Component\Render\FormattableMarkup;

class InsiderStatus extends CPController{

    public function InsiderStatusHome(){

        // 'SELECT campaign_id, description, sitename, 
        //                     campaign_name, siteurl, logourl, siteemail,
        //                     COUNT(IF(mail_sent=1,1,NULL)) as used_lead, 
        //                     COUNT(IF(mail_sent=0,1,NULL)) as unused_lead 
        //                     FROM `leadcampaign` left join 
        //                     campaign using(campaign_id) GROUP by campaign_id'

        $results = $this->dbConnection->query('SELECT campaign_id, IF(c.campaign_id IS NULL, lc.campaign_id, c.campaign_id) as campaign_identifier, description, sitename, campaign_name, customercare_no, siteurl, logourl, siteemail, COUNT(IF(mail_sent=1,1,NULL)) as used_lead, COUNT(IF(mail_sent=0,1,NULL)) as unused_lead FROM `leadcampaign` lc left join campaign c using(campaign_id) GROUP by campaign_id');

        $header = array(
            array('data' => t('Logo'), 'field' => 'logourl'),
            array('data' => t('Description'), 'field' => 'campaign_details'),
            array('data' => t('Website Details'), 'field' => 'campaign_site_details'),
            array('data' => t('Used Lead'), 'field' => 'used_lead'),
            array('data' => t('Unused Lead'), 'field' => 'unused_lead'),
        );

        $rows = [];
        foreach($results as $row) {
            $row = (array) $row;
            $data['logourl'] = !empty($row['logourl']) ?
            new FormattableMarkup('<img src="@$imageUrl" width="72">',['@$imageUrl' => $row['logourl']]) 
            : "";
            $data['campaign_details'] = new FormattableMarkup('<div><div>@campaign_name</div>
            <div>@campaign_identifier</div><div>@campaign_description</div><div>@customercare_no</div></div>',
            ['@campaign_name' => $row['campaign_name'],
            '@campaign_identifier' => $row['campaign_identifier'],
            '@customercare_no' => $row['customercare_no'],
                '@campaign_description' => $row['description']]) ;
            $data['campaign_site_details'] = new FormattableMarkup('<div>
                            <div>@siteurl</div><div>@siteemail</div></div>',
                            ['@siteurl' => $row['siteurl'],
                                '@siteemail' => $row['siteemail']]) ;
           
            $data['used_lead'] = $row['used_lead'];
            $data['unused_lead'] = $row['unused_lead'];
            
            $rows[] = array('data' => $data);
        }

        // \Drupal::messenger()->addMessage('=>'.print_r($this->dbConnection,1));

        $form['mx_accounts']['location_table'] = array(
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

        $query = $this->dbConnection->query('select email_sent_log.date, c.campaign_name, c.sitename, email_sent_log.count, email_sent_log.mail_sent from (SELECT campaign_id, date(timestamp_log) as date, count(*) as count, sum(mail_sent) as mail_sent FROM `leadcampaign` group by `campaign_id`, date(timestamp_log) order by date desc) email_sent_log left join campaign c using(campaign_id) limit 10');
        $result = $query->fetchAll();
        $result = json_decode(json_encode($result),1);
        // \Drupal::messenger()->addMessage(print_r($result,1));
        $rows = [];
        foreach($result as $row) {
            $data = (array) $row;
            $rows[] = array('data' => $data);
        }
    
        $form['email_sent_log'] = array(
            '#markup' => t('Email Sent Status')
        );
        $form['email_sent_log']['location_table'] = array(
            '#theme' => 'table',
            '#header' => $header,
            '#rows' => $rows
        );
        $form['no_cache_input'] = ['#type'=>'hidden','#title'=>time()];

        return $form;
    }

}