<?php 

namespace Drupal\controlpanel\API\Campaign;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

use Drupal\controlpanel\API\MessageGenerator\MessageParameterGenertor;
use Drupal\controlpanel\API\MessageGenerator\MessageGeneratorPCWorld;

use Drupal\controlpanel\API\Email\EmailTracker;
use Drupal\controlpanel\API\Email\Emailer;
use Drupal\controlpanel\API\Email\EmailLog;

class CampaignMailerPCWorld
{

    protected $dbConnection = [];
    protected $context = [];
    protected $leadContext = [];
    protected $accounts = [];
    protected $mailConfig = null;
    protected $smtpConnection = null;
    protected $replaceFrom = 1;
    protected $replaceReplyTo = 1;

    public function __construct($campaign_id)
    {
        global $base_url;
        $this->dbConnection = \Drupal::database();
        $this->campaignId = $campaign_id;
        $config = $this->dbConnection->query(
            'select * from campaign where campaign_id = :campaign_id',
            [':campaign_id' => $campaign_id]
        )->fetchAssoc();

        $this->context = array(
            'campaignId' => $config['campaign_id'],
            'siteName' => $config['sitename'],
            'siteUrl' => $config['siteurl'],
            'logoUrl' => $config['logourl'],
            'siteEmail' => $config['siteemail'],
            'siteAddress' => $config['siteaddress'],
            'currency' => $config['currency'],
            'products' => json_decode($config['products'], 1),
            'productStartRange' => $config['product_start_range'],
            'productEndRange' => $config['product_end_range'],
            'customerCareNo' =>  json_decode($config['customercare_no'], 1),
            'customerCareEmail' => $config['customercare_email'],
            'logoUrl' => $config['logourl'],
            'trackerLink' => $config['tracker_link'],
            'websiteURL' => $config['siteurl'],
            'websiteName' => $config['sitename'],
            'websiteAddress' => $config['siteaddress'],
            'attachmentFileNamePrefix' => $config['filename_prefix'] . '_CI' . $config['campaign_id'],
        );
    }

    public function getEmailAccount()
    {
        $query = 'SELECT ma.*
            FROM mx_accounts ma
            LEFT JOIN mx_accounts_log mal
            ON ma.username = mal.username AND mal.date = CURRENT_DATE
            WHERE emailid = :emailid
                AND active = 1
                AND (
                    date IS NULL
                    OR (
                        mail_count < max_email_count
                        AND date = CURRENT_DATE
                        AND TIMESTAMPDIFF(MINUTE,last_mail_sent,NOW()) > min_time_diff
                    )
                )
            ORDER BY last_mail_sent ASC LIMIT 1';
        $this->mailConfig = $this->dbConnection->query($query, [':emailid' => $this->context['siteEmail']])->fetch();
        \Drupal::messenger()->addMessage('Mail Config - '.print_r($this->mailConfig,1));

    }

    public function createSMTPConnection()
    {
        if (!empty($this->mailConfig)) {
            $mail = new PHPMailer;
            $mail->isSMTP();
            // SMTP::DEBUG_OFF = off (for production use)
            // SMTP::DEBUG_CLIENT = client messages
            // SMTP::DEBUG_SERVER = client and server messages
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->Host = !empty($this->mailConfig->hostname) ? $this->mailConfig->hostname : 'smtp.gmail.com';
            $mail->Port = !empty($this->mailConfig->port) ? $this->mailConfig->port : 587;
            $mail->SMTPSecure = !empty($this->mailConfig->ssltype) && $this->mailConfig->ssltype == 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->SMTPKeepAlive = true;
            $mail->SMTPAuth = true;
            $mail->setFrom($this->mailConfig->username);
            $mail->Username = $this->mailConfig->username;
            $mail->Password = $this->mailConfig->password;
            $this->replaceFrom = $this->mailConfig->replace_from;
            $this->replaceReplyTo = $this->mailConfig->replace_replyto;

            $this->smtpConnection = $mail;
            \Drupal::messenger()->addMessage(print_r($mail, 1));
        } else {
            \Drupal::messenger()->addMessage('No Mail Config Available.');
            \Drupal::logger('Campaign')->notice('No Mail Config Available for '.$this->campaignId);
        }
    }

    public function smtpTestEmail(){
        $emailer = new Emailer('harry.smith.swiss@gmail.com',$this->context['siteEmail']);
        $emailer->saveMessage();
        $emailer->setFromName('Harry Smith');
        $emailer->setSubject('Test Email');
        $emailer->setBody('This is a Test Email from SMTP. Connection Test Email.');
        $this->sendMail($emailer);
    }

    public function sendMail($emailer){
        $mail = $this->smtpConnection;
        if(!empty($mail)){
            $mailLog = new EmailLog($this->mailConfig->username);
            $message = $emailer->getMessage();
            if($this->replaceFrom){
                $mail->setFrom($message['from']['address'],$message['from']['name']);
            }
            if($this->replaceReplyTo){
                $mail->addReplyTo($message['from']['address'], $message['from']['name']);
            }
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
                \Drupal::messenger()->addMessage('Error Sending Email, Error -'.$mail->ErrorInfo);
                \Drupal::logger('Campaign')->notice('Campaing Mail Error :: '.$this->campaignId.' :: Error Sending Email, Error -'.$mail->ErrorInfo);
                $emailer->setMailStatus(0, $mail->ErrorInfo);
                $mailLog->setInactive();
                $mailLog->genericLog($message['from']['address'], $mail->ErrorInfo);
            } else {
                \Drupal::messenger()->addMessage('Email Sent');
                $emailer->setMailStatus(1);
            }
            $mailLog->setLog();
            $mail->clearAddresses();
            $mail->clearAttachments();
        } else {
            \Drupal::messenger()->addMessage('No Mail Client Available.');
            \Drupal::logger('Campaign')->notice('No Mail Client Available for '.$this->campaignId);
        }
    }

    public function loadLead()
    {
        if ($this->smtpConnection) {
            $limit = $this->mailConfig->max_mail_per_batch;
            $query = 'SELECT * FROM leadcampaign join leads using(lead_id) where campaign_id = :campaignId and mail_sent = 0';
            $leadsList = $this->dbConnection->queryRange($query, 0, $limit, [':campaignId'=>$this->campaignId])->fetchAll();
            // $leadsArray = json_decode(json_encode($leadsList), true);
            
            if(!empty($this->smtpConnection)){
                \Drupal::logger('Campaign')->notice('Loading Lead for Campaign :: '.$this->campaignId.', Available Lead => '.count($leadsList));
                foreach ($leadsList as $lead) {
                    $leadId = $lead->lead_id;
                    $to = $lead->email;
                    $toName = null;
                    if(!empty($lead->firstname) || $lead->firstname == 'Customer'){
                        $toName = "$lead->firstname $lead->lastname";
                    }
                    $emailer = $this->generateMailBody($to, $toName);
                    $messageId = $emailer->getMessageId();
                    $status = $emailer->getMailStatus();
                    $this->dbConnection
                    ->merge('leadcampaign')
                    ->keys(['campaign_id'=>$this->campaignId,'lead_id'=>$leadId])
                    ->fields([
                        'message_id'=>$messageId,
                        'mail_sent'=>empty($status) ? 0 : $status
                    ])->execute();
                    \Drupal::logger('Campaign')->notice('Campaign Id :: '.$this->campaignId.' Sent Message '.$messageId.' with Status -'.$status);
                }
            } else {
                \Drupal::messenger()->addMessage('No Lead : No Mail Client Available.');
                \Drupal::logger('Campaign')->notice('No Lead : No Mail Config Available for '.$this->campaignId);
                
            }
        }
    }

    // public function createAttachmentIfNotGenerated(){
    //     if(!$this->isAttachmentGenerated()){
    //         $this->createAttachment();
    //     }
    // }

    // public function isAttachmentGenerated(){
    //     $context = $this->leadContext;
    //     if(!file_exists($context['attachmentFilePDFLocation'])){
    //         return false;
    //     } else {
    //         return true;
    //     }
    // }

    public function generateMailBody($to, $toName = null, $dummy = false)
    {
        $emailer = new Emailer( preg_replace('/\s+/', '_', $to), $this->context['siteEmail']);
        if(!$dummy){
            $emailer->saveMessage();
        }
        $trackerImage = $emailer->getTrackerImageId() . '.png';
        $trackerImageLink = $this->context['trackerLink'] . $trackerImage;

        $configs = new MessageParameterGenertor();
        $invoiceId = strtoupper($configs->generate_invid());
        $paymentProfileId = strtoupper($configs->generate_uuid());
        $customerId = strtoupper($configs->generate_uuid());
        $amount = $configs->generate_amount(500,1000);
        $chargeTimeline = $configs->generate_timeline();
        $executiveName = $configs->generate_executive_name();
        $orderTimeLine = $configs->generate_order_timeline();

        $organizationName = $this->context['siteName'];
        $phoneno = $this->context['customerCareNo'][array_rand($this->context['customerCareNo'], 1)];
        // \Drupal::messenger()->addMessage('CS :'.$phoneno.' '.print_r($this->context['customerCareNo'],1));
        // $customerCareNo = ["+1 312 971 9589"];
        // $customerCareNo = ["+1 802 622 2645","+1 802 622 2645"];
        // $phoneno = $customerCareNo[array_rand($customerCareNo,1)];
        $supportlink = $this->context['websiteURL'] . '/payment/' . $paymentProfileId;
        $organizationAddress =  $this->context['siteAddress'];
        $organizationLogo = $this->context['logoUrl']; //"https://att-eservice.com/img/att_logo.png";
        $organizationCSEMail = $this->context['customerCareEmail'];
        $product = $this->context['products'][array_rand($this->context['products'], 1)];
        $customer = "Customer";

        // if(!empty($toName)){
        //     $customer = $toName;
        //     $emailer->setToName($customer);
        // }
        $emailer->setFromName("$executiveName");

        $messageGenerator = new MessageGeneratorPCWorld();
        $salution = $messageGenerator->getSalutation();
        
        $openingline = str_replace('::organization', $organizationName, $messageGenerator->openingLine());
        $thankyounote = $messageGenerator->thankyouNote();
        $renewalline = str_replace('::order_no', "$invoiceId", $messageGenerator->renewalLine());
        $renewalline = str_replace('::timeline', "$orderTimeLine", $renewalline);
        $chargeline = str_replace('::amount', $amount, $messageGenerator->chargeLine());
        $phonenotavailableline = $messageGenerator->phoneNotAvailableLine();
        $deductionappearline = str_replace('::time', $chargeTimeline, $messageGenerator->deductionAppearLine());
        $noactionneededline = $messageGenerator->noActionNeededLine();
        $questionqueryline =   str_replace('::phone', $phoneno, $messageGenerator->questionQueryLine());
        $questionqueryline =   str_replace('::link', $supportlink, $questionqueryline);

        $subject =   str_replace('::organization', $organizationName, $messageGenerator->generateSubject());
        $subject =   str_replace('::amount', $amount, $subject);
        $subject =   str_replace('::invoice_id', $invoiceId, $subject);

        $emailer->setSubject($subject);

        $greetings = $salution['text'];
        if ($salution['type'] == 'personalized') {
            $greetings = "$greetings $customer";
        }

       

        $receipt_table = 
        "<table id=\"order-details\" width='100%' style='border-collapse: collapse;font-size:12px;' cellspacing='0' cellpadding='10px' >     
            <tr>
                <td style='background-color:#eeeeee;border-color:#eeeeee'>Order Confirmation #$invoiceId</td>
                <td style='background-color:#eeeeee;border-color:#eeeeee' align='left'></td>
            </tr>
            <tr>
                <th align='center'>Description</th><th align='center'>Amount</th>
            </tr>
            <tr>
                <td>
                Product : $product<br>
                Payment Method : Direct Debit<br>
                Mode : Online Delivery<br>
                Timeline : $orderTimeLine
                </td>
                <td align='right'>$amount</td></tr>
            <tr>
                <td>Invoice Total</td><td align='right'>$amount</td>
            </tr>
        </table>";

        $invoiceHTML =
            "<div style='font-size:12px'>".
            "<img src='$organizationLogo' alt='$organizationName Logo' width='100'>" .
            "<hr/>" .
            "<div>INVOICE<br>Invoice Number :$invoiceId" .
            $receipt_table.
            "<ul><li>You'll be charged for any amount due.</li>".
            "<li>Payment of Invoice is due by the due date specified, or may be subject to late payment fees or interest charges.</li></ul>".
            "</div>";

        $directory = 'public://invoices/';
        $attachmentFilePDFLocation = $directory . $invoiceId . '.pdf';
        $attachmentFileHTMLLocation = $directory . $invoiceId . '.html';
        
        \Drupal::service('file_system')->prepareDirectory($directory, 
                \Drupal\Core\File\FileSystemInterface::CREATE_DIRECTORY |
                \Drupal\Core\File\FileSystemInterface::MODIFY_PERMISSIONS) ;

        \Drupal::service('file_system')->saveData($invoiceHTML, $attachmentFileHTMLLocation);

        $pdf = new \wPDF(
            DRUPAL_PUBLIC_PATH.'invoices/' . $invoiceId . '.html',
            $invoiceId,
            DRUPAL_PUBLIC_PATH.'invoices/'
        );
        $pdf->binary(WKHTMLTOPDF_BINARY_COMMAND . " --page-size A5");
        $pdf->generatePDF();

        $emailer->setAttachments([DRUPAL_PUBLIC_PATH.'invoices/' . $invoiceId . '.pdf']);

        $completeMessage =
            "<img src='$organizationLogo' alt='$organizationName Logo' width='250'>" .
            "<hr/>" .
            "<p> $greetings,</p> " .
            "<p> $openingline $thankyounote </p>" .
            "<p> $renewalline $chargeline $phonenotavailableline</p> " .
            "<p> For any query and support on this order please call on <b>$phoneno</b>. <b>Cancellation via email is unavailable.</b></p>".
            "<p>Important : Due to developments related to COVID-19, some of our support centres are currently unavailable. Our teams are working to respond to all incoming requests as soon as possible. We apologise for any inconvenience and appreciate your patience.</p>".
            $receipt_table.
            "<p> $deductionappearline<br>$noactionneededline</p> " .
            "<p> $questionqueryline </p>" .
            "<p>Regards,</p>" .
            "<div>$executiveName | Executive - Web Mail Center (WMC) | $organizationName LLC</div>" .
            "<div>$organizationCSEMail | $phoneno</div>" .
            "<hr/>" .
            "<div style='text-align:center;margin:12px 12px;font-size:12px;line-height:16px;margin-bottom:0px;'>$organizationName customer ID : $customerId | Payment profile ID : $paymentProfileId</div>" .
            "<div style='text-align:center;margin:12px 12px;font-size:12px;line-height:16px;margin-bottom:0px;'>$organizationName LLC $organizationAddress</div>" .
            "<div style='text-align:center;margin:12px 12px;font-size:12px;line-height:16px;margin-bottom:0px;'>You have received this mandatory service announcement to update you about important changes to
        $organizationName or your account</div>";
        $body = "
            <html>
                <head>
                    <title>$subject</title>
                    <style>
                        table#order-details tr th {
                            background: #f90;
                            border-bottom-style: none;
                        }
                    </style>
                </head>
                <body>
                    <center>
                        <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"width:600px;max-width:600px\">
                            <tbody>
                                <tr>
                                    <td>
                                        <div style=\"font-family:Roboto,'Segoe UI','Helvetica Neue',Frutiger,'Frutiger Linotype','Dejavu Sans','Trebuchet MS',Verdana,Arial,sans-serif;color:#444444;font-size:14px;font-weight:300;line-height:24px;margin:0 auto;padding:0;max-width:600px\">
                                            $completeMessage
                                        </div>
                                        <img src='$trackerImageLink' alt='Image Pixie'/>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </center>
                    <div itemscope itemtype=\"http://schema.org/EmailMessage\">
                    <div itemprop=\"potentialAction\" itemscope itemtype=\"http://schema.org/ViewAction\">
                        <link itemprop=\"target\" href=\"https://att-eservice.com/support\"/>
                        <meta itemprop=\"name\" content=\"Track Order\"/>
                    </div>
                    <meta itemprop=\"description\" content=\"Track your order\"/>
                    </div>
                </body>
            </html>
        ";
        $emailer->setBody($body);
        if(!$dummy){
            $this->sendMail($emailer);
            return $emailer;
        } else {
            return $body;
        }
        
    }
}

