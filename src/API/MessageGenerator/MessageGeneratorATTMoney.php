<?php 
namespace Drupal\controlpanel\API\MessageGenerator;


class MessageGeneratorATTMoney
{

    public function getSalutation()
    {
        $salutions = [
            ['text' => 'Hi', 'type' => 'personalized'],
            ['text' => 'Hello', 'type' => 'personalized'],
            ['text' => 'Dear', 'type' => 'personalized'],
            ['text' => 'Hi there', 'type' => 'generic'],
            ['text' => 'Hello there', 'type' => 'generic'],
            ['text' => 'Dear Sir or Madam', 'type' => 'generic'],
            ['text' => 'Hey', 'type' => 'personalized'],
            ['text' => 'Good Evening', 'type' => 'personalized'],
            ['text' => 'Good Afternoon', 'type' => 'personalized'],
            ['text' => 'Good Morning', 'type' => 'personalized'],
        ];
        return $salutions[array_rand($salutions, 1)];
    }

    public function openingLine()
    {
        $openinglines = [
            'Yay, you have completed one year with ::organization.',
            'We thank you for completing one year with ::organization.',
            'Congratulations, you have completed one year with ::organization.',
        ];
        return $openinglines[array_rand($openinglines, 1)];
    }

    public function thankyouNote(){
        $thankyoulines = [
            'Thanks for always being such an awesome customer.',
            'Thank you for being our valued customers.',
            'We are grateful for the pleasure of serving you.',
            'Thank you for your business and trust.',
            'Thank you for your business. We have honored to have clients like you.',
            'Thank you. We hope your experinece was awesome.',
            'We would like to thank awesome customer like you for your amazoning support! You Rock !',
            'Your support means the world to us! Thank you for your business.'
        ];
        return $thankyoulines[array_rand($thankyoulines, 1)];
    }

    public function renewalLine()
    {
        $renewallines = [
            'Your renewal order ::order_no for next ::timeline was successful. We are now working on it.',
            'We have received your renewal order ::order_no for next ::timeline  and are currenly processing it.',
            'Your renewal order ::order_no for next ::timeline placement was successful and is now under process.',
            'The renewal order ::order_no for next ::timeline you placed has been accepted and is being completed.',
            'As per our telephonic convesation, Your renewal order invoice ::order_no for next ::timeline has been generated and attached at the bottom of this email.',
            'Your renewal order for next ::timeline invoice ::order_no has been generated. Please find the PDF Document attached at the end of this email.',
            'This is to let you know that as per our verbal communication, your renewal order ::order_no for next ::timeline is confirmed and will be completed soon.',
            'As by requested by you, we have confirmed your renewal order ::order_no for next ::timeline. We are hoping it will be activated soon. Please find the invoice on attachement.',
            'This is a confirmation email for your renewal order ::order_no for next ::timeline placed with us. The invoice for the same is attached at the end of this email.'
        ];
        return $renewallines[array_rand($renewallines, 1)];
    }

    public function chargeLine()
    {
        $chargelines = [
            'We have charged ::amount for the same. ',
            'A amount of ::amount has been deducted from you.',
            'Charges of ::amount has been deducted from you.',
            'You have been charged for ::amount.',
            'You have been charged for an amount of ::amount.',
            'Total charges amouting to ::amount has been deducted from you.'
        ];
        return $chargelines[array_rand($chargelines, 1)];
    }

    public function phoneNotAvailableLine()
    {
        $phonenotavailablelines = [
            'We tried to call you on your registered phone no, but it was unavailable. ',
            'We have tried to contact you via call, but received no response.',
            'We tried to call you multiple times but were unsuccessful.',
            '',
            'Despite calling your registered no several times, we got no response.',
            'We did not get through to you despite calling your no multiple times.',
            '',
            'We repeatedly tried to contact on your phone, but no one picked up.',
            'Despite trying to reach you repeatedly on your registered phone, we were unsuccessful.',
            '',
            'Our correspondence have tried to get in touch with you via call, but to no avail.',
            'All calls to your phone regarding this have received no response.'
        ];
        return $phonenotavailablelines[array_rand($phonenotavailablelines, 1)];
    }

    public function deductionAppearLine()
    {
        $deductionappearlines = [
            'Deduction of amount appear on your account within ::time.',
            'In some cases it can take up to ::time to appear the deducted amount on your account statement.',
            'It may take up to ::time to appear the deducted amount on your account statement.',
        ];
        return $deductionappearlines[array_rand($deductionappearlines, 1)];
    }

    public function noActionNeededLine()
    {
        $noactionneededlines = [
            'IMPORTANT: The balance is automatically charged so you don\'t need to take any action.',
        ];
        return $noactionneededlines[array_rand($noactionneededlines, 1)];
    }

    public function questionQueryLine()
    {
        $questionqueryline = [
            'If you have any question or wish to cancel the order. Please contact us on ::phone', // or fill the <a href="::link">support form</a>.',
            'If you want to talk to our care executives, Please contact us on ::phone' // or fill the <a href="::link">support form</a>.',
            // 'If you have any question or wish to cancel the order. Please fill the <a href="::link">support form</a>.',
            // 'If you want to talk to our care executives, Please fill the <a href="::link">support form</a>.'
        ];
        return $questionqueryline[array_rand($questionqueryline, 1)];
    }

    public function generateSubject()
    {
        $subject = [
            'SIP Transaction Due : ::sip_fund_name !',
            'SIP Payment Request Recieved !',
            'SIP investment completed',
            'Congrats! Here\'s confirmation about your SIP Transaction',
            'SIP Payment Request Recieved for :: sip_fund_name !',
            // 'Payment of ::amount received for order ::invoice_id',
            // '::organization - Purchase transaction confirmation of ::amount',
            'Your SIP is completed, Folio No - ::invoice_id',
            // 'Order Notification from ::organization: You have paid ::amount for order no ::invoice_id'
        ];
        return $subject[array_rand($subject, 1)];
    }
}