<?php

namespace Drupal\controlpanel\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Component\Render\FormattableMarkup;
use Faker;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

use Drupal\controlpanel\API\Email\EmailLogYahoo;
use Drupal\controlpanel\Controller\CPController;

class DIDCallStatus extends CPController
{
    
    public function deleteItem($item){
        $item->parentNode->removeChild($item);
    }

    public function getFormattedHTMLResult($loggedInPageHTML, $config, $writeToDB = false){
        $html = '';
        try {
            $xml = new \DOMDocument();
            $xml->validateOnParse = false;
            $xml->strictErrorChecking = false;
            @$xml->loadHTML($loggedInPageHTML);
    
            $xpath = new \DOMXPath($xml);
            $classname="tr_hover";
            $search_result = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
            $table =  !empty($search_result) ? $search_result->item(0) : null;

            if(empty($table)){
                $classname="list";
                $search_result = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
                $table =  !empty($search_result) ? $search_result->item(0) : null;
            }

            if(!empty($table)){
                
                $rows = $table->getElementsByTagName("tr");
                $table->setAttribute('class','table table-responsive table-striped table-sm');
                $headerIndexes = [];
                $formattedCells = [];
                foreach ($rows as $row) {
                    $id = $row->getAttribute('id'); 
                    if(substr($id, 0, 23) === 'recording_progress_bar_'){
                        $row->parentNode->removeChild($row);
                    }
                }
                foreach ($rows as $row) {
                    $row->removeAttribute('class');
                    $row->removeAttribute('href');
                    $headers = $row->getElementsByTagName('th');

                    // $this->deleteItem($headers->item(0));
                    
                    foreach ($headers as $headerIndex => $header) {
                        $header->removeAttribute('class');
                        $header->removeAttribute('href');
                        if($writeToDB){
                            $headerName = $header->textContent;
                            if($headerIndex == 1 && $headerName != 'Ext.' ){
                                $headerName = 'call_status';
                            } else if($headerIndex == 0){
                                $headerName = 'call_status';
                            }
                            $headerIdentifier = strtolower(preg_replace('/\s+/', '_', $headerName));
                            $headerIdentifier = trim($headerIdentifier);
                            $headerIdentifier = preg_replace('/\xc2\xa0/','',$headerIdentifier);
                            $headerIndexes[$headerIndex] = $headerIdentifier;
                        }

                        $links = $header->getElementsByTagName('a');
                        foreach ($links as $link) {
                            $newDiv = $xml->createElement ("div", $link->nodeValue);
                            $link->parentNode->replaceChild ($newDiv, $link);
                        } 
                    }
                    

                    $cells = $row->getElementsByTagName('td');
                    // $this->deleteItem($cells->item(0));

                    $formattedCell = [];
                    foreach ($cells as $cellIndex => $cell) {
                        $cell->removeAttribute('class');
                        $cell->removeAttribute('href');
                        $images = $cell->getElementsByTagName('img');
                        $title = '';
                        foreach ($images as $image) {
                            $title = $image->getAttribute('title');
                            switch($title){
                                case 'Inbound: Answered':
                                        $image->setAttribute('src', '/sites/all/themes/controlpanel_subtheme/phone.svg');
                                        break;
                                case 'Inbound: Cancelled':
                                        $image->setAttribute('src', '/sites/all/themes/controlpanel_subtheme/missed_call.svg');
                                        break;
                                case 'Inbound: Failed':
                                    $image->setAttribute('src', '/sites/all/themes/controlpanel_subtheme/end_call.svg');
                                    break;
                                default:
                                    $image->setAttribute('src', 'http://most.nmcomm.me/'.$image->getAttribute('src'));
                            }   
                        }
                        $links = $cell->getElementsByTagName('a');
                        foreach ($links as $link) {
                            $newDiv = $xml->createElement ("div", $link->nodeValue);
                            $link->parentNode->replaceChild ($newDiv, $link);
                        } 
                        if($writeToDB){
                            if(!empty($headerIndexes[$cellIndex])){
                                $formattedCell[$headerIndexes[$cellIndex]] = preg_replace('/\xc2\xa0/','',trim($cell->textContent).$title);
                            }
                        }
                    }
                    $formattedCells[] = $formattedCell;
                }
                // \Drupal::messenger()->addMessage('=>'.print_r($headerIndexes,1));
                // \Drupal::messenger()->addMessage('=>'.$writeToDB);
                // \Drupal::messenger()->addMessage('=>'.json_encode($formattedCells,1));
                
                if($writeToDB){
                    $dbConnection = \Drupal::database();
                    foreach($formattedCells as $fields){
                        $fields['account_id'] = $config->did_id;
                        if(isset($fields['start'])){
                            $fields['start'] = date ('Y-m-d H:i:s', strtotime($fields['start']));
                        } else if(isset($fields['date']) && isset($fields['time'])){
                            $datetime = $fields['date'].' '.str_replace('&colon;',':',$fields['time']);
                            // \Drupal::messenger()->addMessage('=>'.json_encode($datetime,1));
                            $fields['start'] = date ('Y-m-d H:i:s', strtotime($datetime));
                            unset($fields['date']);
                            unset($fields['time']);
                            unset($fields['ext.']);
                        }
                        \Drupal::messenger()->addMessage('=>'.print_r($fields,1));
                        try{
                            $dbConnection->merge('mc__call_details_records')->key(
                                [
                                    'account_id'=>$fields['account_id'],
                                    'caller_number'=>$fields['caller_number'],
                                    'caller_destination'=>$fields['caller_destination '],
                                    'destination'=>$fields['destination'],
                                    'start'=>$fields['start'],
                                ]
                            )->fields($fields)->execute();
                        } catch (\Exception $e){
                            \Drupal::messenger()->addMessage('=>'.$e->getMessage());
                        }
                    }
                }
                $html = $xml->saveXML($table);
            }
        } catch (\Exception $e){
            return $e->getMessage();
        }
        return $html;
    }

    public function getCIDRLoginCURL($config, $writeToDB = false){
        $userAgent = 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2309.372 Safari/537.36';
        $cookieJar = DRUPAL_PUBLIC_PATH."did_cookie_$config->did_no.txt";
        //An associative array that represents the required form fields.
        //You will need to change the keys / index names to match the name of the form
        //fields.
        $postValues = array(
            'username' => $config->username,
            'password' => $config->password
        );
        
        //Initiate cURL.
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $config->actionurl);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postValues));
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_COOKIEJAR, $cookieJar);
        curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_REFERER, $config->loginurl);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
        return $curl;
    }
    
    public function getDIDCDRDetails($config, $writeToDB = false){
        $userAgent = 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2309.372 Safari/537.36';
        $cookieJar = DRUPAL_PUBLIC_PATH."did_cookie_$config->did_no.txt";
        $curl = $this->getCIDRLoginCURL($config, $writeToDB);
        curl_exec($curl);
        if(curl_errno($curl)){
            throw new \Exception(curl_error($curl));
        }
        curl_setopt($curl, CURLOPT_URL,$config->dataurl);
        curl_setopt($curl, CURLOPT_COOKIEJAR, $cookieJar);
        curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        //direction=&call_result=&caller_id_number=&destination_number=&start_stamp_begin=2020-07-01+00%3A00&start_stamp_end=2020-07-30+00%3A00&caller_id_name=&hangup_cause=&caller_destination=&submit=Search
        $loggedInPageHTML = curl_exec($curl);

        return $this->getFormattedHTMLResult($loggedInPageHTML, $config, $writeToDB);
        
    }

    public function getDIDCDRsDetailsWithFilter($config, $start_date, $end_date, $writeToDB = false){
        $userAgent = 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2309.372 Safari/537.36';
        $cookieJar = DRUPAL_PUBLIC_PATH."did_cookie_$config->did_no.txt";
        $curl = $this->getCIDRLoginCURL($config, $writeToDB);
        curl_exec($curl);
        if(curl_errno($curl)){
            throw new \Exception(curl_error($curl));
        }
        curl_setopt($curl, CURLOPT_COOKIEJAR, $cookieJar);
        curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $html = '';
        $logginURL = $config->dataurl;

        for($page = 0; $page < 8 ; $page ++){
            $filter = '?page='.$page.'&cdr_id=&missed=&direction=&caller_id_name=&caller_id_number='.
            '&caller_destination=&caller_extension_uuid=&destination_number=&context=&'.
            'start_stamp_begin='.$start_date.'%2000:00&start_stamp_end='.$end_date.'%2000:00&answer_stamp_begin='.
            '&answer_stamp_end=&end_stamp_begin=&end_stamp_end=&start_epoch=&stop_epoch=&duration=&billsec=&'.
            'hangup_cause=&call_result=&xml_cdr_uuid=&bleg_uuid=&accountcode=&read_codec=&write_codec=&remote_media_ip='.
            '&network_addr=&bridge_uuid=&mos_comparison=&mos_score=&order_by=&order=';
            curl_setopt($curl, CURLOPT_URL, $logginURL.$filter);
            $loggedInPageHTML = curl_exec($curl);
            $html .= $this->getFormattedHTMLResult($loggedInPageHTML, $config, $writeToDB);
        }

        return $html;
    }

    public function DIDCallStatusHomeLiveFilter($campaign_id, $start_date, $end_date){

        $dbConnection = \Drupal::database();
        $account = $dbConnection->query('SELECT * FROM `mc__did_accounts` where did_id = :did_id',[':did_id'=>$campaign_id])->fetch(); 

        if(!empty($account)){
            $form['title'] = ['#markup' =>"<p class=\"h3\"><img src='/sites/all/themes/controlpanel_subtheme/online_support.svg' width='36'> $account->did_no</p>
            <p>You can see the <mark>Formatted</mark> details of this DID No.</p>
            <div>"];
            $form['table'] = ['#markup' =>$this->getDIDCDRsDetailsWithFilter($account, $start_date, $end_date, true)];
        } else {
            $form['table'] = ['#markup' =>'<div>You don\'t have permission to view this page. Please contact administrator for more information.</div>'];
        }
        $form['hidden_input'] = ['#type'=>'hidden','value'=>'cdr'];
        $form['file_modified'] = ['#markup' =>'<div>DIDCallStatusHomeLiveFilter Last Modified - '.gmdate("Y-m-d\TH:i:s\Z", filemtime(__FILE__)).'</div>'];
        return $form;
    }

    public function DIDCallStatusHome($campaign_id = 0){
        $dbConnection = \Drupal::database();
        $account = $dbConnection->query('SELECT * FROM `mc__did_accounts` where did_id = :did_id',[':did_id'=>$campaign_id])->fetch(); 
        
        if(!empty($account)){
            $now = new \DateTime();
            $begin = new \DateTime('9:00');
            $date_change = new \DateTime('18:30');
            $end = new \DateTime('8:59:59');
            
            // if ($now >= $begin && $now <= $date_change){
                $end->add(new \DateInterval('P1D'));
            // } else {
                // $begin->sub(new \DateInterval('P1D'));
            // }
            // $end->add(new \DateInterval('P1D'));
            $begin->setTimezone(new \DateTimeZone('Asia/Kolkata'));
            $end->setTimezone(new \DateTimeZone('Asia/Kolkata'));
            // $begin = new \DateTime('14:30',new \DateTimeZone('Asia/Kolkata'));
            
            

            $start_date = $begin->format('Y-m-d H:i:s');
            $end_date = $end->format('Y-m-d H:i:s');
            $fields = [':account_id'=>$account->did_id,':start_date'=>$start_date, ':end_date'=>$end_date];
            $console_count = $dbConnection->query("SELECT count(*) FROM `mc__call_details_records` WHERE account_id = :account_id and start BETWEEN :start_date and :end_date and (MINUTE(duration) >= 20 OR HOUR(duration) >= 1) ORDER BY `mc__call_details_records`.`duration` DESC",$fields)->fetchField();
            $total_call_count = $dbConnection->query("SELECT count(*) as total_call_count FROM `mc__call_details_records` WHERE account_id = :account_id and start BETWEEN :start_date and :end_date",$fields)->fetchField();
            $missed_call_count = $dbConnection->query("SELECT count(*) as missed_failed_call_count FROM `mc__call_details_records` WHERE account_id = :account_id and start BETWEEN :start_date and :end_date AND call_status IN ('Inbound: Cancelled','Inbound: Failed')",$fields)->fetchField();

            $form['title'] = ['#markup' =>"<p class=\"h3\"><img src='/sites/all/themes/controlpanel_subtheme/online_support.svg' width='36'> $account->did_no</p>
            <p>You can see the <mark>Live</mark> details of this DID No. ($start_date - $end_date)</p>
            <div>"];
            $form['cards'] = [
                '#markup'=>'
                <div class="row">
                    <div class="col-md-4">
                        <div class="card text-white bg-dark mb-3">
                            <div class="card-header">Console Count</div>
                            <div class="card-body">
                                <h5 class="card-title">'.$console_count.'</h5>
                                <p class="card-text">Call Duration with more than 20 minutes.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-secondary mb-3">
                            <div class="card-header">Total Call Count</div>
                            <div class="card-body">
                                <h5 class="card-title">'.$total_call_count.'</h5>
                                <p class="card-text">Total Call from 14:30 PM</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                    <div class="card bg-light mb-3">
                        <div class="card-header">Missed Call Count</div>
                        <div class="card-body">
                            <h5 class="card-title">'.$missed_call_count.'</h5>
                            <p class="card-text">Missed Call from 14:30 PM</p>
                        </div>
                    </div>
                </div>
            </div>'
            ];
            
            $header = array(
                array('data' => t(''), 'field' => 'call_status'),
                array('data' => t('Caller Name'), 'field' => 'caller_name'),
                array('data' => t('Caller Number'), 'field' => 'caller_number '),
                array('data' => t('Caller Destination'), 'field' => 'caller_destination'),
                array('data' => t('Destination'), 'field' => 'destination'),
                array('data' => t('Recording'), 'field' => 'recording'),
                array('data' => t('Start'), 'field' => 'start'),
                array('data' => t('TTA'), 'field' => 'tta'),
                array('data' => t('Duration'), 'field' => 'duration'),
                array('data' => t('PDD'), 'field' => 'pdd'),
                array('data' => t('MOS'), 'field' => 'mos'),
                array('data' => t('Hangup Cause'), 'field' => 'hangup_cause')
            );
    
            $query = $dbConnection->query('SELECT `call_status`, `caller_name`, `caller_number`, `caller_destination`, 
            `destination`, `recording`, `start`, `tta`, `duration`, `pdd`, `mos`, `hangup_cause`, (MINUTE(duration) >= 20 OR HOUR(duration) >= 1 ) as console
            FROM `mc__call_details_records` where account_id = :account_id and start >= DATE_ADD(CURDATE(), INTERVAL -1 DAY) order by start desc',[':account_id'=>$account->did_id]);
            $result = $query->fetchAll();
    
            $rows = [];
            foreach($result as $row) {
                $data = (array) $row;
                switch($data['call_status']){
                    case 'Inbound: Answered':
                            $imageUrl = '/sites/all/themes/controlpanel_subtheme/phone.svg';
                            break;
                    case 'Inbound: Cancelled':
                            $imageUrl =  '/sites/all/themes/controlpanel_subtheme/missed_call.svg';
                            break;
                    case 'Inbound: Failed':
                            $imageUrl =  '/sites/all/themes/controlpanel_subtheme/end_call.svg';
                            break;
                    default:
                            $imageUrl = '/sites/all/themes/controlpanel_subtheme/call_transfer.svg';
                }   
                
                $data['call_status'] = new FormattableMarkup('<img src="@$imageUrl" width="16">',['@$imageUrl' => $imageUrl]);
                $data['duration'] = $data['console'] == 1 ? new FormattableMarkup('@duration <img src="/sites/all/themes/controlpanel_subtheme/rating.svg" width="16"> ',['@duration' => $data['duration']]) : $data['duration'];
                $class = $data['console'] == 1 ? 'table-info' : '';
                unset($data['console']);
                $rows[] = array('data' => $data,'class'=>$class);
            }
            
            $form['mx_accounts']['location_table'] = array(
                '#theme' => 'table',
                '#header' => $header,
                '#rows' => $rows,
                '#attributes' => [
                    'class' => ['table table-responsive table-striped table-sm'],
                ]
            );
        }
        $form['hidden_input'] = ['#type'=>'hidden','value'=>'cdr'];
        $form['file_modified'] = ['#markup' =>'<div>Last Modified - '.gmdate("Y-m-d\TH:i:s\Z", filemtime(__FILE__)).'</div>'];
        return $form;

    }

    public function DIDCallStatusHomeAll(){
        $dbConnection = \Drupal::database();
        $accounts = $dbConnection->query('SELECT * FROM `mc__did_accounts`')->fetchAll(); 
        foreach($accounts as $account){
            $this->getDIDCDRDetails($account, true);
        }
        $form['hidden_input'] = ['#type'=>'hidden','value'=>'cdr'];
        $form['file_modified'] = ['#markup' =>'<div>Last Modified - '.gmdate("Y-m-d\TH:i:s\Z", filemtime(__FILE__)).'</div>'];
        return $form;
    }
    
    public function DIDCallStatusHomeLive($campaign_id = 0){

        $dbConnection = \Drupal::database();
        $account = $dbConnection->query('SELECT * FROM `mc__did_accounts` where did_id = :did_id',[':did_id'=>$campaign_id])->fetch(); 

        if(!empty($account)){
            $form['title'] = ['#markup' =>"<p class=\"h3\"><img src='/sites/all/themes/controlpanel_subtheme/online_support.svg' width='36'> $account->did_no</p>
            <p>You can see the <mark>Formatted</mark> details of this DID No.</p>
            <div>"];
            $form['table'] = ['#markup' =>$this->getDIDCDRDetails($account, true)];
        } else {
            $form['table'] = ['#markup' =>'<div>You don\'t have permission to view this page. Please contact administrator for more information.</div>'];
        }
        $form['hidden_input'] = ['#type'=>'hidden','value'=>'cdr'];
        $form['file_modified'] = ['#markup' =>'<div>Last Modified - '.gmdate("Y-m-d\TH:i:s\Z", filemtime(__FILE__)).'</div>'];
        return $form;
    }
}