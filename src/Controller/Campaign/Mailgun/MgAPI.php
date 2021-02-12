<?php

namespace Drupal\controlpanel\Controller\Campaign\Mailgun;

use Drupal\Core\Controller\ControllerBase;
use Drupal\controlpanel\Controller\CPController;
use Symfony\Component\HttpFoundation\JsonResponse;
// use Drupal\controlpanel\API\Campaign\CampaignMailerYahoo;
use Drupal\controlpanel\API\MessageGenerator\MessageParameterGenertor;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Faker;
use Mailgun\Mailgun;
// use Faker;

/**
 * Implements an example form.
 * 
 * 
 */

class MgAPI extends CPController {
    
    public function mailgunEventsAPI(){
        $faker = Faker\Factory::create();

        $templates = [];
        $emailLines = [];
        $data = [];

        $templatesLines = [
            ['OPENING_LINE','THANKYOU_LINE'],
            ['ORDERCONFIRM_LINE','CHARGE_LINE','SUPPORT_LINE'],
            ['DISCLAIMER_LINE']
        ];

        $configs = new MessageParameterGenertor();

        $data['::order_no'] = strtoupper($configs->generate_invid());
        $data['::organization'] = $faker->company;
        $data['::product_name'] = 'GSquad';
        $data['::timeline'] = $configs->generate_order_timeline();
        $data['::amount'] = $configs->generate_amount();
        $data['::userAgent'] = $faker->userAgent;
        $data['::executive_name'] = "$faker->firstName $faker->lastName";
        $data['::department'] = $faker->jobTitle;
        $data['::address'] = $faker->address;

        $data['::tracker_image'] = '';
        $data['::subject'] = '';
        $data['::link'] = '';
        $data['::phone'] = '';

        foreach($templatesLines as $lineIndex => $templateLine){
            $templates[$lineIndex] =  $templates[$lineIndex] || [];
            $emailLines[$lineIndex] =  $emailLines[$lineIndex] || [];
            foreach($templateLine as $dbTemplate){
                $templateResult = $this->dbConnection->query("select * from email__message_template_lines 
                where type = :type order by RAND() limit 1",[':type'=>$dbTemplate])->fetch();
                if(!empty($templateResult->template)){
                    $templates[$lineIndex][$dbTemplate] = $templateResult;
                    $emailLines[$lineIndex][]= trim($templateResult->template,'.');
                }
            }
        }

        $emailLines[][] = 'Important : Due to developments related to COVID-19, some of our support centres are currently unavailable. Our teams are working to respond to all incoming requests as soon as possible. We apologise for any inconvenience and appreciate your patience.';

        $emailParagraphs = [];
        foreach($emailLines as $emailLine){
            $emailParagraphs[] = '<p>'.implode('. ', $emailLine).'.</p>';
        }
        $email = implode('', $emailParagraphs);
        $email = "
        <html>
            <head>
                <title>::subject</title>
            </head>
                <body>
                    <center>
                        <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"width:600px;max-width:600px\">
                            <tbody>
                                <tr>
                                    <td>
                                        <div style=\"font-family:Roboto,'Segoe UI','Helvetica Neue',Frutiger,'Frutiger Linotype','Dejavu Sans','Trebuchet MS',Verdana,Arial,sans-serif;color:#444444;font-size:14px;font-weight:300;line-height:24px;margin:0 auto;padding:0;max-width:600px\">
                                            $email
                                        </div>
                                       <table width='100%' style='border-collapse: collapse;font-size:12px;' cellspacing='0' cellpadding='10px' >     
                                            <tr>
                                                <td style='background-color:#eeeeee;border-color:#eeeeee'>Order Confirmation #$invoiceId</td>
                                                <td style='background-color:#eeeeee;border-color:#eeeeee' align='left'></td>
                                            </tr>
                                            <tr>
                                                <th align='center'>Description</th><th align='center'>Amount</th>
                                            </tr>
                                            <tr>
                                                <td>
                                                Product : ::product_name<br>
                                                Valid for Next ::timeline
                                                </td>
                                                <td align='right'>::amount</td></tr>
                                            <tr>
                                                <td>Invoice Total</td><td align='right'>::amount</td>
                                            </tr>
                                        </table>
                                        <img src='::tracker_image' alt='Image Pixie'/>
                                        <p>
                                        <div>Regards,</div>
                                        <div>::executive_name ( ::organization ) </div>
                                        <div>::address </div>
                                        <div> ::link | ::phone</div>
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </center>
                </body>
            </html>
        ";
        $email = strtr($email, $data);

        // $openingLinesTemplate = $this->dbConnection->query("select * from email__message_template_lines 
        //             where type = 'OPENING_LINE' order by RAND() limit 1")->fetch();
        // $thankyouLinesTemplate = $this->dbConnection->query("select * from email__message_template_lines 
        //         where type = 'THANKYOU_LINE' order by RAND() limit 1")->fetch();
        // $orderConfirmLinesTemplate = $this->dbConnection->query("select * from email__message_template_lines 
        //         where type = 'ORDERCONFIRM_LINE' order by RAND() limit 1")->fetch();


        // $data['template']['opening_line'] = trim($openingLinesTemplate->template,'.');
        // $data['template']['thankyou_line'] = trim($thankyouLinesTemplate->template,'.');
        // $data['template']['orderconfirm_line'] = trim($orderConfirmLinesTemplate->template,'.');
        // $data['replaced_message'] = strtr(implode('. ',$data['template']), $data);
        // $mail = new PHPMailer();
        // $mail->isSMTP();
        // $mail->SMTPOptions = array(
        //     'ssl' => array(
        //         'verify_peer' => false,
        //         'verify_peer_name' => false,
        //         'allow_self_signed' => true
        //     )
        // );
        // $mail->SMTPDebug = SMTP::DEBUG_LOWLEVEL;
        // $mail->setFrom('billing@netflix-esolutions.com', 'Netflix Esolutions');
        // $mail->addAddress('dan247cra1@gmail.com', 'Joe User');     // Add a recipient
        // // $mail->addAddress('ellen@example.com');               // Name is optional
        // // $mail->addReplyTo('info@example.com', 'Information');
        // // $mail->addCC('cc@example.com');
        // // $mail->addBCC('bcc@example.com');
        // $mail->Subject = 'Here is the subject';
        // $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
        // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        // $mail->send();
        
        // $mail = new PHPMailer;
        // $mail->isSMTP();
        // // SMTP::DEBUG_OFF = off (for production use)
        // // SMTP::DEBUG_CLIENT = client messages
        // // SMTP::DEBUG_SERVER = client and server messages
        // $mail->SMTPDebug = SMTP::DEBUG_LOWLEVEL;
        // // $mail->Host = 'smtp.gmail.com';
        // // $mail->Port = 587;

        // // $mail->Host = 'smtp.office365.com';
        // $mail->Host = 'smtp.gmail.com';
        // $mail->Port = 587;
        // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        // // $mail->SMTPKeepAlive = true;

        // // $mail->Username = 'kasshiya337th438@gmail.com';
        // // $mail->Password = 'kolkata123@';

        // // $mail->setFrom('kasshiya337th438@gmail.com', '24x7ITech');
        // $mail->setFrom('billing@netflix-esolutions.com', '24x7ITech');
        // $mail->addAddress('dan247cra1@gmail.com');
        // $mail->Subject = 'Test Subject';
        // $mail->msgHTML('Test Subject');
        // // $mail->SMTPAuth = true;

        // if (!$mail->send()) {

        // } 
        
        // # Instantiate the client.
        // $mgClient    = Mailgun::create('6249a0aa39fa83973f131ab7ba085c9d-0f472795-225de2df');

        // // $mgClient->setDebug(true);

        // $domain      = 'mg.iserv.space';
        // $queryString = array(
        //     'begin'        => 'Wed, 8 Sep 2020 09:00:00 -0000',
        //     'ascending'    => 'yes',
        //     'limit'        =>  25,
        //     'pretty'       => 'yes',
        //     'event' => 'failed'
        // );

        // # Issue the call to the client.
        // $result['last_updated_version'] = filemtime(__FILE__);
        // $result['queryString'] = $queryString;
        // $result['mailgun_response'] = $mgClient->events()->get($domain, $queryString);
        // $result['mailgun_bounce_response'] = $mgClient->suppressions()->bounces()->index($domain);
        $form['no_cache'] = ['#type'=>'hidden'];
        $form['email']['#markup'] = $email;
        return $form;
        return new JsonResponse([$data, $templates, $email]);
    }
}
