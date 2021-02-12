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
        // \Drupal::messenger()->addMessage('After Render -> '.$html->__toString().'<=>'.print_r($html,1) );

        // $leadsMailer = new LeadsMailer(4);
        // $leadsMailer->selectLeadById(2000);
        // $leadsMailer->sendEmailWithAttachment();
        // $leadsMailer->selectLeadById(2001);
        // $leadsMailer->sendEmailWithAttachment();

        $mail = new PHPMailer();
        $mail->setFrom('billing@netflix-esolutions.com', 'Netflix Esolutions');
        $mail->addAddress('harry.smith.swiss@gmail.com', 'Joe User');     // Add a recipient
        // $mail->addAddress('ellen@example.com');               // Name is optional
        // $mail->addReplyTo('info@example.com', 'Information');
        // $mail->addCC('cc@example.com');
        // $mail->addBCC('bcc@example.com');
        $mail->Subject = 'Here is the subject';
        $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        
        
        return ['#markup'=>'New config => '.$mail->ErrorInfo];
    }

    public function emailCron(){
        \Drupal::logger('conrolpanel')->notice('Cron Job is running.');
        // $this->getLeadsV2();
        return ['#markup'=>'Email Running'];
    }

    public function getLeadsV2($campaign = '', $config = []){
        global $base_url;
        $connection = \Drupal::database();
        $query = 'select l.*, ANY_VALUE(lc.campaign_id) from leads l left join leadcampaign lc using (lead_id) where campaign_id <> :campaign or campaign_id IS NULL and lead_id < 1422 group by lead_id';
        $leadsList = $connection->queryRange($query,0, 50, 
                                        [':campaign'=>4])->fetchAll();
                                        $leadsArray = json_decode(json_encode($leadsList ), true);
                                        \Drupal::messenger()->addMessage('Response -'.print_r($leadsArray , 1));
        $leadsMailer = new LeadsMailer(4);
        foreach($leadsArray as $lead){
            
            $leadsMailer->setLeadInfo($lead);
            $leadsMailer->sendEmailWithAttachment();
        }
    }


    // public function emailBodyTemplate($context){
    //     $styles = file_get_contents(dirname(__DIR__, 1).'/templates/css/template3.css');
    //     \Drupal::messenger()->addMessage('Style Found -> '.dirname(__DIR__, 1).'/templates/css/template3.css =>'.$styles );

    //     $form = [];
    //     $form['html'] = [
    //         '#prefix'=>'<html>',
    //         '#suffix'=>'</html>'
    //     ];
    //     $form['html']['head'] = $this->headTemplate($context);
    //     $form['html']['body'] = [
    //         '#prefix'=> '<body class="">
    //                         <span class="preheader">'.$context['subject'].'</span>
    //                             <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
    //                                 <tbody><tr>
    //                                     <td>&nbsp;</td>
    //                                     <td class="container">
    //                                         <div class="content">',
    //         '#suffix'=>'</div>
    //                         </td>
    //                         <td>&nbsp;</td>
    //                     </tr>
    //                 </tbody></table>
    //             </body>'
    //     ];
    //     $form['html']['body']['content'] = $this->contentTemplate($context);
    //     $form['html']['body']['footer'] = $this->footerTemplate($context);
    //     $html = $renderer->render($form);
    //     return $html;
    // }

    

    // public function phpmailerMail($fromName, $subject, $to, $toName, $body, $attachment, $fileName){
    //     $mail = new PHPMailer;
    //     $mail->isSMTP();
    //     // SMTP::DEBUG_OFF = off (for production use)
    //     // SMTP::DEBUG_CLIENT = client messages
    //     // SMTP::DEBUG_SERVER = client and server messages
    //     $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    //     $mail->Host = 'smtp.gmail.com';
    //     $mail->Port = 587;
    //     $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    //     $mail->SMTPAuth = true;


    //     $mail->Username = 'billing@att-eservice.com';
    //     $mail->Password = 'kolkata123@';


    //     $mail->setFrom('billing@att-eservice.com',$fromName);
    //     $mail->addReplyTo('billing@att-eservice.com', $fromName);


    //     $mail->addAddress($to, $toName);
    //     $mail->Subject = $subject;
    //     $mail->msgHTML($body);
    //     $mail->AltBody = $subject;
    //     $flag = $mail->addAttachment($attachment, $fileName);
    //     //send the message, check for errors
    //     if (!$mail->send()) {
    //         \Drupal::messenger()->addMessage('Response -'.$mail->ErrorInfo);
    //     } else {
    //         \Drupal::messenger()->addMessage('Mail Sent');
    //     }
    // }

    // public function createMessage($receiverId, $receiverName, $receiverEmailAddress, $subject, $body, $attachments = []){
    //         $message = [];
    //         $message['from'] = ['name'=>$websiteName, 'address'=>$websiteEmailAddress];
    //         $message['to'] = ['name'=>$receiverName, 'address'=>$receiverEmailAddress];
    //         $message['subject'] = $subject;
    //         $message['body'] = $body;
    //         $message['attachments'] = $attachments;
    // }

    // public function doMail($message, $account){
    //     $mail = new PHPMailer;
    //     $mail->isSMTP();
    //     // SMTP::DEBUG_OFF = off (for production use)
    //     // SMTP::DEBUG_CLIENT = client messages
    //     // SMTP::DEBUG_SERVER = client and server messages
    //     $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    //     $mail->Host = 'smtp.gmail.com';
    //     $mail->Port = 587;
    //     $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    //     $mail->SMTPAuth = true;


    //     $mail->Username = $account['username'];
    //     $mail->Password = $account['password'];


    //     $mail->setFrom($message['from']['address'],$message['from']['address']);
    //     $mail->addReplyTo($message['from']['address'], $message['from']['name']);


    //     $mail->addAddress($message['to']['address'], $message['to']['name']);
    //     $mail->Subject =$message['subject'];
    //     $mail->msgHTML($message['body']);
    //     $mail->AltBody = $message['subject'];
        
    //     foreach($message['attachments'] as $attachment){
    //         $attachmentPath = \Drupal::service('file_system')->realpath($attachment);
    //         $mail->addAttachment($attachmentPath);
    //     }
    //     $flag = false;
    //     //send the message, check for errors
    //     if (!$mail->send()) {
    //         // return false;
    //         \Drupal::messenger()->addMessage('Response -'.$mail->ErrorInfo);
    //     } else {
    //         // return true;
    //         \Drupal::messenger()->addMessage('Mail Sent');
    //     }
    //     $mail->clearAddresses();
    //     $mail->clearAttachments();
    // }

    // public function contentTemplate($context){
    //     $form['body'] = [];
    //     $form['body']['#prefix'] = '<table role="presentation" class="main"><tbody>';
    //     $form['body']['#suffix'] = '</tbody></table>';

    //     $form['body']['logo'] = array(
    //         '#prefix'=> '<tr><td class="wrapper">',
    //         '#type' => 'inline_template',
    //         '#template' => '<table><tbody><tr><td><img src="{{logoUrl}}" width="150px"></td><td></td></tr></tbody></table>',
    //         '#context' => $context ,
    //         '#suffix'=> '</td></tr>'
    //     );

    //     $form['body']['content'] = array(
    //         '#prefix'=> '<tr><td class="wrapper">',
    //         '#suffix'=> '</td></tr>'
    //     );
    //     $form['body']['content']['template'] = $this->contentBodyTemplate($context);
    //     return $form;
    // }

    // public function headTemplate($context){
    //     $context['style'] = file_get_contents(dirname(__DIR__, 1).'/templates/css/'.$context['cssTemplate']);
    //     $template = '
    //         <head>
    //             <meta name="viewport" content="width=device-width">
    //             <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    //             <title>{{subject}}</title>
    //             <style>
    //                 {{style}}
    //             </style>
    //         </head>
    //     ';
    //     return $form['head'] = array(
    //         '#type' => 'inline_template',
    //         '#template' => $template,
    //         '#context' => $context ,
    //     );
    // }

    // public function footerTemplate($context){
    //     $template = '
    //         <div class="footer">
    //             <table role="presentation" border="0" cellpadding="0" cellspacing="0">
    //                 <tbody><tr>
    //                     <td class="content-block">
    //                         <a href="{{websiteURL}}/unsubscribe">Unsubscribe</a> |
    //                         <a href="{{websiteURL}}/help">Help</a>
    //                     </td>
    //                 </tr>
    //                 <tr>
    //                     <td class="content-block">
    //                         <span class="apple-link">You are receiving {{websiteName}} notification emails.</span>
    //                         <br> Don\'t like these emails?

    //                     </td>
    //                 </tr>
    //                 <tr>
    //                     <td class="content-block">
    //                         This email was intended for {{receiverName}} ({{receiverEmail}}). <br>
    //                         <a href="{{websiteURL}}/email-footer">Learn why we included this.</a>
    //                     </td>
    //                 </tr>
    //                 <tr>
    //                     <td class="content-block">
    //                         © 2019 {{websiteName}}, {{websiteAddress}}.
    //                         {{websiteName}} is a registered business name of {{websiteName}} LLC.
    //                         {{websiteName}} and the {{websiteName}} logo are registered trademarks of {{websiteName}} LLC.
    //                     </td>
    //                 </tr>
    //             </tbody></table>
    //         </div>';

    //     return $form['footer'] = array(
    //         '#type' => 'inline_template',
    //         '#template' => $template,
    //         '#context' => $context ,
    //     );
    // }

    // public function contentBodyTemplate($context){
    //     $template = '<table role="presentation" border="0" cellpadding="0" cellspacing="0">
    //         <tbody>
    //             <tr>
    //                 <td>
    //                     <p>Hi {{receiverName}},</p>
    //                     {{startParagraph}}
    //                     <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
    //                         <tbody>
    //                             <tr>
    //                                 <td align="left">
    //                                     <table role="presentation" border="0" cellpadding="0" cellspacing="0">
    //                                         <tbody>
    //                                             <tr><td><a href="{{callToActionLink}}" target="_blank">{{callToActionText}}</a></td></tr>
    //                                         </tbody>
    //                                     </table>
    //                                 </td>
    //                             </tr>
    //                         </tbody>
    //                     </table>
    //                     {{endParagraph}}
    //                 </td>
    //             </tr>
    //         </tbody>
    //     </table>';

    //     return $form['body'] = array(
    //         '#type' => 'inline_template',
    //         '#template' => $template,
    //         '#context' => $context ,
    //     );
    // }

    // public function sendEmail($sendGridAPI, $campaign_id, $logoUrl, $leadId, $name, $lead_email, $site_name,
    //  $invoice_no, $cs_no, $siteurl, $org_address, $sitemail, $attachment, $filename){
    //     $connection = \Drupal::database();
    //     $email = new \SendGrid\Mail\Mail(); 
    //     $email->setFrom($sitemail, $site_name);
    //     $email->setSubject('Your '.$site_name.' order has been received!');
    //     $email->addTo($lead_email, $name);
    //     $body = email_template('#', $site_name, $logoUrl, 'Your '.$site_name.' order has been received!', 
    //     $name, $lead_email, 
    //     '<p>Just to let you know — we\'ve received your order '.$invoice_no.', and it is now being processed.</p>
    //     <p>In case you have any queries / clarifications, please call us at our Customer Service number.</p>', 
    //     'tel://'.$cs_no, $cs_no, 
    //     '<p>Thanks for using '.$siteurl.'!</p>', $org_address);
    //     $this->phpmailerMail('Tom Cruise', 'Your '.$site_name.' order has been received!', $lead_email, 
    //     $name, $body, DRUPAL_PUBLIC_PATH.$attachment, $filename);
        
    //     $email->addContent(
    //         "text/html",$body 
            
    //     );
    //     if($attachment){
    //         $file_encoded = base64_encode(file_get_contents($attachment));
    //         $email->addAttachment(
    //             $file_encoded,
    //             "application/pdf",
    //             $filename.".pdf",
    //             "attachment"
    //         );
    //     }
    //     $sendgrid = new \SendGrid($sendGridAPI );
    //     try {
    //         // $response = $sendgrid->send($email);
    //         // \Drupal::messenger()->addMessage($body);
    //         // \Drupal::messenger()->addMessage('Response -'.print_r($response, 1));
    //         $connection->merge('leadcampaign')->key(array(
    //             'campaign_id'=> $campaign_id,
    //             'lead_id' => $leadId,
    //         ))
    //         ->fields(['mail_sent'=>1])
    //         ->execute();
    //         return true;
    //     } catch (Exception $e) {
    //         return false;
    //     }
    // }

 
    // public function getLeads($campaign = '', $config = []){
    //     global $base_url;
    //     $connection = \Drupal::database();
    //     $config = $connection->query('select * from campaign where default_campaign = 1')->fetchAssoc();
    //     \Drupal::messenger()->addError('Config-'.print_r($config, 1));

    //     $campaignId = $config['campaign_id'];
    //     $startLimit = $config['startlimit'];
    //     $endLimit = $config['endlimit'];
    //     $siteName = $config['sitename'];
    //     $siteUrl = $config['siteurl'];
    //     $logoUrl = $config['logourl'];
    //     $siteEmail = $config['siteemail']; 
    //     $siteAddress = $config['siteaddress'];
    //     $currency = $config['currency'];
    //     $products = json_decode($config['products'],1);
    //     $productStartRange = $config['product_start_range'];
    //     $productEndRange = $config['product_end_range'];
    //     $customerCareNo = $config['customercare_no'];
    //     $sendGridAPI = $config['sendgrid_api'];

    //     $query = 'select l.*, ANY_VALUE(lc.campaign_id) from leads l left join leadcampaign lc using (lead_id) where campaign_id <> :campaign or campaign_id IS NULL group by lead_id';
    //     $leadsList = $connection->queryRange($query,$startLimit, $endLimit, 
    //                                     [':campaign'=>$campaignId])->fetchAll();

    //     foreach($leadsList as $lead){
    //         $invoiceNo = 'IVBXFF_'.$campaignId.'_'.$lead->lead_id;
    //         $fileName = 'Invoice_'.$invoiceNo;
    //         $destinationUri = 'public://' . $fileName;
    //         if(!file_exists($destinationUri.'.pdf')){
    //             \Drupal::messenger()->addError('Invoice Not Exists - '.$lead->lead_id);
    //             if(!file_exists($destinationUri.'.html')){
    //                 \Drupal::messenger()->addError('HTML Not Exists - '.$lead->lead_id);
    //                 $htmlInvoice = invoice_template($siteName, $invoiceNo, $logoUrl, 
    //                     date('F d Y'), date('F d Y', strtotime(' +1 day')),
    //                     $siteAddress, titleCase($lead->firstname).' '.titleCase($lead->lastname), 
    //                     titleCase($lead->address).', '.titleCase($lead->county).', '.titleCase($lead->state).', '.$lead->zip, 
    //                     $lead->email, $currency, $products, $productStartRange, $productEndRange);
    //                 \Drupal::service('file_system')->saveData($htmlInvoice, $destinationUri.'.html');
    //             }
    //             $pdf = new \wPDF(DRUPAL_PUBLIC_PATH. $fileName . '.html',
    //             $fileName, DRUPAL_PUBLIC_PATH);
    //             $pdf->binary(WKHTMLTOPDF_BINARY_COMMAND." --page-size A4");
    //             $pdf->generatePDF();
    //         } else {
    //             \Drupal::messenger()->addError('Send Email to User.');
    //             $this->sendEmail($sendGridAPI, $campaignId, $logoUrl, $lead->lead_id, titleCase($lead->firstname).' '.titleCase($lead->lastname), $lead->email, $siteName, 
    //                 $invoiceNo, $customerCareNo, $siteUrl, $siteAddress, $siteEmail, $destinationUri.'.pdf', $invoiceNo);
    //         }
    //     }
    // }

    // public function mailjetEmailSender(){
    //     $mj = new \Mailjet\Client('a10779ff4c8c40215b590f25d5c13735','1145684c9abcbc28249a3442f7fff7f4',true,['version' => 'v3.1']);
    //     $body = [
    //         'Messages' => [
    //         [
    //             'From' => [
    //             'Email' => "tomc7898@gmail.com",
    //             'Name' => "Tom"
    //             ],
    //             'To' => [
    //             [
    //                 'Email' => "tomc7898@gmail.com",
    //                 'Name' => "Tom"
    //             ]
    //             ],
    //             'Subject' => "Greetings from Mailjet.",
    //             'TextPart' => "My first Mailjet email",
    //             'HTMLPart' => "<h3>Dear passenger 1, welcome to <a href='https://www.mailjet.com/'>Mailjet</a>!</h3><br />May the delivery force be with you!",
    //             'CustomID' => "AppGettingStartedTest"
    //         ]
    //         ]
    //     ];
    //     $response = $mj->post(\Mailjet\Resources::$Email, ['body' => $body]);
    //     return $response->success() && $response->getData();
    // }

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
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->SMTPKeepAlive = true;
        $mail->SMTPAuth = true;

        $mail->Username = 'billing@att-eservice.com';
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
            // $mail->setFrom('billing@att-eservice.com',$fromName);
            // $mail->addReplyTo('billing@att-eservice.com', $fromName);


            $mail->setFrom($message['from']['address'],$message['from']['name']);
            $mail->addReplyTo($message['from']['address'], $message['from']['name']);


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