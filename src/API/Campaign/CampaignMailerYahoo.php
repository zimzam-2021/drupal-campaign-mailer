<?php 

namespace Drupal\controlpanel\API\Campaign;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

use Drupal\controlpanel\API\MessageGenerator\MessageParameterGenertor;
use Drupal\controlpanel\API\MessageGenerator\MessageGeneratorPCWorld;

use Drupal\controlpanel\API\Email\EmailTracker;
use Drupal\controlpanel\API\Email\Emailer;
use Drupal\controlpanel\API\Email\EmailLogYahoo;

use Faker;

use Drupal\controlpanel\API\Campaign\CampaignMailer;

class CampaignMailerYahoo extends CampaignMailer
{

    public function getEmailAccount()
    {
        $query = ' SELECT *
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
        ORDER BY `mal`.`test_mail_count`  DESC, mal.last_mail_sent desc limit 1;';
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
            $mail->Host = 'smtp.mail.yahoo.com';
            $mail->Port = 465;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->SMTPAuth = true;
            $mail->SMTPKeepAlive = true;
            $mail->setFrom($this->mailConfig->username);
            $mail->Username = $this->mailConfig->username;
            $mail->Password = $this->mailConfig->password;
            $this->replaceFrom = 0;
            $this->replaceReplyTo = 0;
            $this->mailConfig->max_mail_per_batch = 2;

            $this->smtpConnection = $mail;
            \Drupal::messenger()->addMessage(print_r($mail, 1));
        } else {
            \Drupal::messenger()->addMessage('No Mail Config Available.');
            \Drupal::logger('Campaign')->notice('No Mail Config Available for '.$this->campaignId);
        }
    }

    public function getMailLogger($username){
        return new EmailLogYahoo($username);
    }
    
    public function generateMailBody($to, $toName = null, $dummy = false)
    {
        $emailer = new Emailer( preg_replace('/\s+/', '_', $to), $this->context['siteEmail']);
        $faker = Faker\Factory::create();
        if(!$dummy){
            $emailer->saveMessage();
        }
        $trackerImage = $emailer->getTrackerImageId() . '.png';
        $trackerImageLink = $this->context['trackerLink'] . $trackerImage;

        $configs = new MessageParameterGenertor();
        $invoiceId = strtoupper($configs->generate_invid());
        $paymentProfileId = strtoupper($configs->generate_uuid());
        $customerId = strtoupper($configs->generate_uuid());
        $amount = $configs->generate_amount();
        $chargeTimeline = $configs->generate_timeline();
        $executiveName = $configs->generate_executive_name();
        $orderTimeLine = $configs->generate_order_timeline();

        $organizationName = $this->context['siteName'];
        $customerCareNo = ["+1 814 631 5005"];
        // $customerCareNo = ["+1 802 622 2645","+1 802 622 2645"];
        $phoneno = $this->context['customerCareNo'][array_rand($this->context['customerCareNo'], 1)];
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
        "<table width='100%' style='border-collapse: collapse;font-size:12px;' cellspacing='0' cellpadding='10px' >     
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
            <tr>
                <td align='right' colspan='2'>
                    MAC Address : $faker->macAddress<br>
                    User Agent : $faker->userAgent
                </td>
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
            "<img src='$organizationLogo' alt='$organizationName Logo' width='100'>" .
            "<hr/>" .
            "<p> $greetings,</p> " .
            "<p> $openingline $thankyounote </p>" .
            "<p> $renewalline $chargeline $phonenotavailableline</p> " .
            "<p> For any query and support on this order please call on <b>$phoneno</b>.
                <b>Cancellation via email is unavailable.</b>
            </p>".
            "<p>Important : Due to developments related to COVID-19, some of our support centres are currently unavailable. Our teams are working to respond to all incoming requests as soon as possible. We apologise for any inconvenience and appreciate your patience.</p>".
            $receipt_table.
            "<div style=\"font-size:4px;line-height:1px;\">".$faker->realText(rand(200, 800), rand(1,5))."</div>".
            "<p> $deductionappearline<br>$noactionneededline</p> " .
            "<p> $questionqueryline </p>" .
            "<div style=\"font-size:4px;line-height:1px;\">".$faker->realText(rand(200, 800), rand(1,5))."</div>".
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
                                        <div style=\"font-size:4px;line-height:1px;\">".$faker->realText(rand(200, 800), rand(1,5))."</div>
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

