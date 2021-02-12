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
use Drupal\controlpanel\API\SMTP_validateEmail;
use Symfony\Component\HttpFoundation\Response;

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
  
function base64url_decode($data) {
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}




class EmailTracker {
    protected $to = null;
    protected $from = null;
    protected $messageId = null;
    protected $imageId = null;
    
    public function __construct($to=null, $from=null, $messageId=null){
        $this->to = $to;
        $this->from = $from;
        $this->messageId = $messageId;
    }

    public function getImageId(){
        $this->generateImageId();
        return $this->imageId;
    }

    public function generateImageId(){
        $imageInformation = [
            'to' => $this->to,
            'from' => $this->from,
            'messageId' => $this->messageId
        ];
        $this->imageId = base64url_encode(json_encode($imageInformation));
    }

    public function decodeImageId(){
        if(!empty($this->imageId)){
            $mailPartJSON = base64url_decode($this->imageId);
            $imageInformation = json_decode($mailPartJSON,1);
            \Drupal::logger('controlpanel')->notice('Decoded Id - '.print_r($imageInformation,1));
            $this->to = $imageInformation['to'];
            $this->from = $imageInformation['from'];
            $this->messageId = $imageInformation['messageId'];
        }
    }

    public function setImageId($image){
        $imageParts = pathinfo($image);
        if($imageParts['extension'] == 'png'){
            $this->imageId = $imageParts['filename'];
            $this->decodeImageId();
        }
    }

    public function serveImage(){
        // header('Content-Type: image/png');
        return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII=');
    }
}

class EMailer{
    protected $connection;
    protected $to = null;
    protected $from = null;
    protected $messageId = null;

    public function __construct($to, $from){
        $this->connection = \Drupal::database();
        $this->to = $to;
        $this->from = $from;
        $this->messageId = null;
        $this->trackerImageId = null;
    }

    public function saveMessage(){
        $this->messageId = $this->connection->insert('email_message')
        ->fields([
            'to_user'=> $this->to,
            'from_user' => $this->from,
        ])->execute();
        $tracker = new EmailTracker($this->to, $this->from, $this->messageId);
        $this->trackerImageId = $tracker->getImageId();
        \Drupal::logger('controlpanel')->notice('Decoded Id - '.print_r($id,1));
    }

    public function setSubject(){
        $this->subject = $subject;
    }

    // public function setBody

    public function createMessage($receiverName, $receiverEmail, $siteName, $siteEmail, $subject, $body, $attachments = []){
        $message = [];
        $message['from'] = ['name'=>$siteName, 'address'=>$siteEmail];
        $message['to'] = ['name'=>$receiverName, 'address'=>$receiverEmail];
        $message['subject'] = $subject;
        $message['body'] = $body;
        $message['attachments'] = $attachments;
        return $message;
    }
}

class Home extends ControllerBase {

    public function homeRouter(){
        $renderer = \Drupal::service('renderer');

        // $this->phpmailerMail('Tom Cruise', 'Test Email with Attachment 3', 'harry.smith.swiss@gmail.com', 
        //     'Harry Smith', 'This is Test API Mail with Attachement', '/opt/bitnami/apps/drupal/htdocs/sites/default/files/Invoice_IVBXFF_1_12289.pdf','Invoice_IVBXFF_1_12289.pdf');
        // $context = array(
        //     'logoUrl'=>'http://104.154.23.188/core/themes/bartik/logo.svg',
        //     'receiverName' => 'Tim Barton',
        //     'receiverEmail' => 'Tim Barton',
        //     'startParagraph' => 'You are the creator of Web' ,
        //     'endParagraph' => 'Thank You',
        //     'callToActionLink' => 'http://google.com',
        //     'callToActionText' => 'Click here to visit Google',
        //     'websiteURL'=>'24x7ITSolutions.org',
        //     'websiteName'=>'24x7 IT Solutions',
        //     'websiteAddress'=>'24, Private Drive, Surray, UK',
        //     'cssTemplate'=>'template3.css',
        //     'subject'=>'Email Subject'
        // );

        // $accounts =[
        //     'username'=>'billing@att-eservice.com',
        //     'password'=>'kolkata123@'
        // ]

        // $html = emailBodyTemplate($context);
        // // \Drupal::messenger()->addMessage('After Render -> '.$html->__toString().'<=>'.print_r($html,1) );

        // $leadsMailer = new LeadsMailer(4);
        // $leadsMailer->selectLeadById(3103);
        // $leadsMailer->sendEmailWithAttachment();

        // $leadsMailer = new LeadsMailer(4);
        // $leadsMailer->selectLeadById(2001);
        // $leadsMailer->sendEmailWithAttachment();

        // $leadsMailer = new LeadsMailer(4);
        // $leadsMailer->selectLeadById(2002);
        // $leadsMailer->sendEmailWithAttachment();
        // $this->getLeadsV2();
        
        // $mail = new PHPMailer();
        // $mail->isSMTP(); 
        // $mail->Host = 'localhost';
        // $mail->Port = 25;
        // $mail->SMTPSecure = "tls";
        // $mail->SMTPOptions = array
        // (
        // 'ssl' => array
        // (
        //     'verify_peer' => false,
        //     'verify_peer_name' => false,
        //     'allow_self_signed' => true
        // )
        // );
        // // $mail->Port = $Port;
        // $mail->setFrom('billing@netflix-esolutions.com', 'Netflix Esolutions');
        // $mail->addAddress('harry.smith.swiss@gmail.com', 'Joe User');     // Add a recipient
        // // $mail->addAddress('ellen@example.com');               // Name is optional
        // // $mail->addReplyTo('info@example.com', 'Information');
        // // $mail->addCC('cc@example.com');
        // // $mail->addBCC('bcc@example.com');
        // $mail->Subject = 'Here is the subject';
        // $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
        // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        // $mail->send();
        
        
        // return ['#markup'=>'New config => '.$mail->ErrorInfo];

        // the email to validate  
        $emails = ['bhunter55@att.net','bhunter55@aol.com','bhunter55@gmail.com',
                'jordanbanshee78@gmail.com','jordanbanshee78@outlook.com','jordanbanshee78@aol.com'];  
        // an optional sender  
        $sender = 'user@fqcn-chiloo.com';  
        // instantiate the class  
        $SMTP_Valid = new SMTP_validateEmail();  
        // do the validation  
        $result = $SMTP_Valid->validate($emails, $sender);  
        // view results  
        // var_dump($result);  
        // echo $email.' is '.($result ? 'valid' : 'invalid')."\n";  

        // send email?   
        // if ($result) {  
        //mail(...);  
            \Drupal::messenger()->addMessage($email.' is '.($result ? 'valid' : 'invalid')."\n".print_r($result,1)."<br>".print_r( $SMTP_Valid->debug,1));
        // }

        return ['#markup'=>'Validate'];
    }

    public function imagePixie($image){
        \Drupal::logger('controlpanel')->notice('Pixie - '.$image);
        $tracker = new EmailTracker('sitanath@gmail.com', 'oldmonk@gmail.com',1);
        $emailer = new Emailer('sitanath@gmail.com', 'oldmonk@gmail.com');
        $emailer->saveMessage();
        $imageID = $tracker->getImageId();
        $imageID = $tracker->setImageId($image);
        \Drupal::logger('controlpanel')->notice('Image Id - '.$tracker->getImageId());
         return new Response(
            $tracker->serveImage(),
            Response::HTTP_OK,
            ['content-type' => 'image/png']
        );
    }

    public function leadFormatter(){
        \Drupal::messenger()->addMessage('Lead Formatter is Running !!!');
        $connection = \Drupal::database();
        $query = 'select * from leads where username is null';
        $leadsList = $connection->queryRange($query,0, 100)->fetchAll();
        $fields = [];
        foreach($leadsList as $lead){
            $email = $lead->email;
            $emailParts = explode("@",$email);
            if(count($emailParts)==2){
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

    public function emailAddressValidator($count = 0){
        \Drupal::messenger()->addMessage('Email Address Validator is Running !!!');
        $connection = \Drupal::database();
        $query = 'select * from leads where username is not null and cron_check_count = :count';
        $leadsList = $connection->queryRange($query,0, 10,[':count'=>$count])->fetchAll();
        $fieldsList = [];
        $emails = [];
        foreach($leadsList as $lead){
            $fieldsList[$lead->email] = [
                        'lead_id'=>$lead->lead_id,
                        'user_exists'=>$lead->user_exists,
                        'cron_check_count'=>$lead->cron_check_count];
            array_push($emails, $lead->email);
        } 
        $sender = 'postmaster@app.netflix-esolutions.com';   
        $SMTP_Valid = new SMTP_validateEmail();  
        $results = $SMTP_Valid->validate($emails, $sender); 
        foreach($results as $email => $status){
            if(!empty($fieldsList[$email])){
                $lead_id = $fieldsList[$email]['lead_id'];
                unset($fieldsList[$email]['lead_id']);
                $fields = $fieldsList[$email];
                if(empty($fields['user_exists'])){
                    $fields['user_exists'] = 0;
                }
                $fields['user_exists']+= $status;
                $fields['cron_check_count']++;
                $connection->merge('leads')->key(array(
                    'lead_id' => $lead_id,
                ))
                ->fields($fields)
                ->execute();
            }
            
        }
    }

    public function cronEvery5Minutes(){
        $this->emailAddressValidator(0);
        return ['#markup'=>'Cron Running 5 Minutes '];
    }

    public function cronEvery10Minutes(){
        $this->emailAddressValidator(1);
        return ['#markup'=>'Cron Running 10 Minutes '];
    }

    public function cronEvery15Minutes(){
        $this->emailAddressValidator(2);
        return ['#markup'=>'Cron Running 15 Minutes '];
    }

    public function cronEvery30Minutes(){
        // $this->emailAddressValidator();
        return ['#markup'=>'Cron Running 30 Minutes '];
    }

    public function emailCron(){
        \Drupal::logger('conrolpanel')->notice('Cron Job is running.');
        $this->leadFormatter();
        return ['#markup'=>'Email Running'];
    }

    public function getLeadsV2($campaign = '', $config = []){
        global $base_url;
        $connection = \Drupal::database();
        $query = 'select l.*, ANY_VALUE(lc.campaign_id) from leads l left join leadcampaign lc using (lead_id) where campaign_id <> :campaign or campaign_id IS NULL and lead_id > 2401 and lead_id < 2802 group by lead_id';
        $leadsList = $connection->queryRange($query,0, 100, 
                                        [':campaign'=>4])->fetchAll();
                                        $leadsArray = json_decode(json_encode($leadsList ), true);
                                        \Drupal::messenger()->addMessage('Response -'.print_r($leadsArray , 1));
        $leadsMailer = new LeadsMailer(4);
        foreach($leadsArray as $lead){
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

class LeadsMailer {

    protected $dbConnection = [];
    protected $context = [];
    protected $leadContext = [];
    protected $accounts = [];
    protected $mailConfig = null;

    public function __construct($campaign_id){
        global $base_url;
        $this->dbConnection = \Drupal::database();
        $this->campaignId = $campaign_id;
        $config = $this->dbConnection->query('select * from campaign where campaign_id = :campaign_id',
                [':campaign_id'=>$campaign_id])->fetchAssoc();

        \Drupal::messenger()->addError('Config-'.print_r($config, 1));

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
        \Drupal::logger('conrolpanel')->notice('Current Cache State - '.$currentUserNameIndex);
        if(empty($current)){
            $currentUserNameIndex = 0;
        }
        $emailAccountUserName = $userNames[$currentUserNameIndex];
        \Drupal::logger('conrolpanel')->notice('Current Cache UserName - '.$currentUserNameIndex.'=>'.$emailAccountUserName);
        $currentUserNameIndex++;
        if($currentUserNameIndex > 3){
            $currentUserNameIndex = 0;
        }
        \Drupal::cache()->set('currentUserNameIndex', $currentUserNameIndex);
        \Drupal::logger('conrolpanel')->notice('Current Email Username - '.$currentUserNameIndex.'=>'.$emailAccountUserName);
        $mail->Username = $emailAccountUserName;
        $mail->Password = 'kolkata123@';

        $this->mailConfig = $mail;


        

        $this->context = array(
            'campaignId'=>$config['campaign_id'],
            'startLimit'=>$config['startlimit'],
            'endLimit'=>$config['endlimit'],
            'siteName'=>$config['sitename'],
            'siteUrl'=>$config['siteurl'],
            'logoUrl'=>$config['logourl'],
            'siteEmail'=>$config['siteemail'], 
            'siteAddress'=>$config['siteaddress'],
            'currency'=>$config['currency'],
            'products' => json_decode($config['products'],1),
            'productStartRange'=>$config['product_start_range'],
            'productEndRange'=>$config['product_end_range'],
            'customerCareNo'=>$config['customercare_no'],
            'apiCredentials'=>$config['api_credentials'],
            'logoUrl'=>$config['logourl'],
            'startParagraph' => $this->safeRenderHTML($config['start_paragraph']),
            'endParagraph' => $this->safeRenderHTML($config['end_paragraph']),
            'startParagraphRaw' => $config['start_paragraph'],
            'endParagraphRaw' => $config['end_paragraph'],
            'callToActionLink' => $config['call_to_action_link'],
            'callToActionText' => $config['call_to_action_text'],
            'websiteURL'=>$config['siteurl'],
            'websiteName'=>$config['sitename'],
            'websiteAddress'=>$config['siteaddress'],
            'cssTemplate'=>$config['css_template'],
            'subjectTemplate'=>$config['subject_template'],
            'attachmentFileNamePrefix'=>$config['filename_prefix'].'_CI'.$config['campaign_id'],
        );

        $this->accounts = [
            'username'=>'billing@att-eservice.com',
            'password'=>'kolkata123@'
        ];
    }

    public function safeRenderHTML($valueTemplate){
        $renderer = \Drupal::service('renderer');
        return $form['footer'] = array(
            '#type' => 'inline_template',
            '#template' => $valueTemplate,
            '#context' => [] ,
        );
        $html = $renderer->render($form);
        return $html;
    }


    public function getContext(){
        return $this->context;
    }

    public function selectLeadById($lead_id){
        $this->dbConnection = \Drupal::database();
        $lead = $this->dbConnection->query('select * from leads where lead_id = :lead_id',
                [':lead_id'=>$lead_id])->fetchAssoc();
        $this->setLeadInfo($lead);
    }

    public function setLeadInfo($lead){
        $this->leadContext = [];
        $context = $this->context;
        $this->leadId = $lead['lead_id'];
        $context['receiverName'] = ucwords(strtolower($lead['firstname'].' '.$lead['lastname']));
        $context['receiverEmail'] = $lead['email'];
        $context['receiverAddress'] = ucwords(strtolower($lead['address'].', '.$lead['county'].', '.$lead['state'].', '.$lead['zip']));
        $context['invoiceDate'] =  date('F d Y');
        $context['invoiceDueDate'] =  date('F d Y', strtotime(' +1 day'));
        $context['attachmentFileName'] = $context['attachmentFileNamePrefix'].'_'.$lead['lead_id'];
        $context['attachmentFilePDFLocation'] = 'public://'.$context['attachmentFileName'].'.pdf';
        $context['attachmentFileHTMLLocation'] = 'public://'.$context['attachmentFileName'].'.html';
        $this->leadContext = $context;
    }

    public function createEmail(){
        $context = $this->leadContext;
        $context['emailBody'] = email_template($context['siteUrl'],$context['siteName'], $context['logoUrl'], 
        $context['subjectTemplate'].' ('. $context['receiverEmail'].')', $context['receiverName'], $context['receiverEmail'], $context['startParagraphRaw'], 
        $context['callToActionLink'], $context['callToActionText'], $context['endParagraphRaw'], $context['websiteAddress']);
        
        //$this->emailBodyTemplate($context);
        $context['message'] = $this->createMessage(
                                    $context['receiverName'], $context['receiverEmail'], $context['siteName'], $context['siteEmail'],
                                    $context['subjectTemplate'].' ('. $context['receiverEmail'].')', $context['emailBody'],[$context['attachmentFilePDFLocation']]);

                                    
        $this->leadContext = $context;
    }

    

    
    public function createMessage($receiverName, $receiverEmail, $siteName, $siteEmail, $subject, $body, $attachments = []){
        $message = [];
        $message['from'] = ['name'=>$siteName, 'address'=>$siteEmail];
        $message['to'] = ['name'=>$receiverName, 'address'=>$receiverEmail];
        $message['subject'] = $subject;
        $message['body'] = $body;
        $message['attachments'] = $attachments;
        return $message;
    }

    public function doMail($message, $account){
        $mail = $this->mailConfig;
        if(!empty($mail)){
            $mail->setFrom('jordanbanshee78@outlook.com','24x7ITech');
            // $mail->addReplyTo('billing@att-eservice.com', $fromName);


            // $mail->setFrom($message['from']['address'],$message['from']['name']);
            // $mail->addReplyTo($message['from']['address'], $message['from']['name']);


            $mail->addAddress($message['to']['address'], $message['to']['name']);
            $mail->Subject =$message['subject'];
            $mail->msgHTML($message['body']);
            $mail->AltBody = $message['subject'];
            
            foreach($message['attachments'] as $attachment){
                $attachmentPath = \Drupal::service('file_system')->realpath($attachment);
                $mail->addAttachment($attachmentPath);
                \Drupal::messenger()->addMessage($attachmentPath);
            }
            //send the message, check for errors
            if (!$mail->send()) {
                \Drupal::messenger()->addMessage('Response -'.$mail->ErrorInfo);
                // return false;
                
            } else {
                $connection = \Drupal::database();
                $connection->merge('leadcampaign')->key(array(
                    'campaign_id'=> $this->campaignId,
                    'lead_id' => $this->leadId,
                ))
                ->fields(['mail_sent'=>1])
                ->execute();
                \Drupal::messenger()->addMessage('Mail Sent');
                // return true;
                
            }
            \Drupal::logger('conrolpanel')->notice(
                'Mail Sent to '.$message['to']['address'].' Error Status -'.$mail->ErrorInfo
            );
            $mail->clearAddresses();
            $mail->clearAttachments();
        }
    }

    public function sendEmailWithAttachment(){
        $this->createAttachmentIfNotGenerated();
        if($this->isAttachmentGenerated()){
            $this->createEmail();
            if(!empty($this->leadContext['message'])){
                $this->doMail($this->leadContext['message'], $this->accounts);
            }

        }
    }

    public function createAttachment(){
        $context = $this->leadContext;
        if(!file_exists($context['attachmentFileHTMLLocation'])){
            $htmlInvoice = invoice_template(
                    $context['siteName'], $context['attachmentFileName'], $context['logoUrl'], 
                    $context['invoiceDate'],$context['invoiceDueDate'], $context['siteAddress'],
                    $context['receiverName'], $context['receiverAddress'], $context['receiverEmail'],
                    $context['currency'], $context['products'], $context['productStartRange'], $context['productEndRange']);
            \Drupal::service('file_system')->saveData($htmlInvoice, $context['attachmentFileHTMLLocation']);
        }
        $pdf = new \wPDF(DRUPAL_PUBLIC_PATH. $context['attachmentFileName'] . '.html',
        $context['attachmentFileName'], DRUPAL_PUBLIC_PATH);
        $pdf->binary(WKHTMLTOPDF_BINARY_COMMAND." --page-size A4");
        $pdf->generatePDF();
    }

    public function createAttachmentIfNotGenerated(){
        if(!$this->isAttachmentGenerated()){
            $this->createAttachment();
        }
    }

    public function isAttachmentGenerated(){
        $context = $this->leadContext;
        if(!file_exists($context['attachmentFilePDFLocation'])){
            return false;
        } else {
            return true;
        }
    }

    public function emailBodyTemplate($context){
        $renderer = \Drupal::service('renderer');
        $styles = file_get_contents(dirname(__DIR__, 1).'/templates/css/template3.css');
        \Drupal::messenger()->addMessage('Style Found -> '.dirname(__DIR__, 1).'/templates/css/template3.css =>'.$styles );
        
        $form['head']= $this->headTemplate($context);
        $form['body'] = [
            '#prefix'=> '
                            <span class="preheader">'.$context['subject'].'</span>
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
                                    <tbody><tr>
                                        <td>&nbsp;</td>
                                        <td class="container">
                                            <div class="content">',
            '#suffix'=>'</div>
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                    </tbody></table>
                '
        ];
        $form['body']['content'] = $this->contentTemplate($context);
        $form['body']['footer'] = $this->footerTemplate($context);
        $html = '<html>'.$renderer->render($form['head']).'<body>'.$renderer->render($form['body']).'</body></html>';
        return $html;
    }

    
    public function contentTemplate($context){
        $form['body'] = [];
        $form['body']['#prefix'] = '<table role="presentation" class="main"><tbody>';
        $form['body']['#suffix'] = '</tbody></table>';

        $form['body']['logo'] = array(
            '#prefix'=> '<tr><td class="wrapper">',
            '#type' => 'inline_template',
            '#template' => '<table><tbody><tr><td><img src="{{logoUrl}}" width="150px"></td><td></td></tr></tbody></table>',
            '#context' => $context ,
            '#suffix'=> '</td></tr>'
        );

        $form['body']['content'] = array(
            '#prefix'=> '<tr><td class="wrapper">',
            '#suffix'=> '</td></tr>'
        );
        $form['body']['content']['template'] = $this->contentBodyTemplate($context);
        return $form;
    }

    public function headTemplate($context){
        $context['style'] = file_get_contents(dirname(__DIR__, 1).'/templates/css/'.$context['cssTemplate']);
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
            '#context' => $context ,
        );
    }

    public function footerTemplate($context){
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
            '#context' => $context ,
        );
    }

    public function contentBodyTemplate($context){
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
            '#context' => $context ,
        );
    }
}