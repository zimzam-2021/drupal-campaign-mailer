<?php 
namespace Drupal\controlpanel\API\MessageGenerator;

class NortonMessageGenerator
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
            'Yay, your order ::order_no was successful. We are now working on it.',
            'We have received your order ::order_no and are currenly processing it.',
            'Congratulations, your order ::order_no placement was successful and is now under process.',
            'The order ::order_no you placed has been accepted and is being completed.',
            'The order ::order_no you placed has been accepted and is being completed.',
            'As per our telephonic convesation, Your order invoice ::order_no has been generated and attached at the bottom of this email.',
            'Your annual invoice ::order_no has been generated. Please find the PDF Document attached at the end of this email.',
            'This is to let you know that as per our verbal communication, your order ::order_no is confirmed and will be completed soon.',
            'As by requested by you, we have confirmed your order ::order_no. We are hoping it will be activated soon. Please find the invoice on attachement.',
            'This is a confirmation email for your order ::order_no placed with us. The invoice for the same is attached at the end of this email.',
            'This is to confirm the payment of the following Invoices/Debit Notes:  ::order_no ',
            'Welcome to ::product_name. Your new account comes with access to ::product_name products, apps and services.  Here are a few tips to get you started.',
            'Stay up to date with the ::product_name app. Find quick answers, explore your interests and stay up to date.',
            'You have successfully started your ::product_name Merchant Account Application. You can save your progress and resume the application using the steps below:',
            'Your ::product_name solution has been deployed on Cloud Platform.',
            'You have successfully completed your Signup. Follow the steps outlined in this email to explore and kickstart your ::product_name Account:',
            'Please note details of Funds Received from you for order ::order_no',
            'Please note details of Pending Invoice for you for order ::order_no',
            'Your transaction AddFund-::order_no done through Direct Debit was flagged as high risk in our risk assessment check, due to discrepancies in the profile and billing information. Thus, we have decided to refund it.',
            'We have finished creating a copy of the ::product_name data that you requested.',
            'You’re getting this email because there’s been a request to create an archive of your ::product_name  data. If you didn’t make this request, someone may be trying to access your ::product_name  Account. Check recent activity in your account and take steps to secure it.',
            'I really appreciate you joining us at ::product_name. We are here to help you identify, fix, and prevent deliverability issues.',
            'Thank you for expressing your interest in ::product_name.',
            'Your order item with order no ::order_no is delivered',
            'You are holding a e-support account ::order_no with ::product_name.',
            'It is our pleasure to have you as the part of ::product_name.',
            'We value your association and look forward to serving you many years to come.',
            'It is our constant endeavor to equip you with the information that helps you offer the best services.',
            'We are writing you to let you know that we have updated our payment charge policy to comply with applicable situations.',
            'A New device has been added to ::product_name service plan.',
            'This is to inform you that your account is due for annual maintanance charge (AMC)'
        ];
        return $openinglines[array_rand($openinglines, 1)];
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
            'If you have any question or wish to cancel the order. Please contact us on ::phone or fill the <a href="::link">support form</a>.',
            'If you want to talk to our care executives, Please contact us on ::phone or fill the <a href="::link">support form</a>.',
            // 'If you have any question or wish to cancel the order. Please fill the <a href="::link">support form</a>.',
            // 'If you want to talk to our care executives, Please fill the <a href="::link">support form</a>.'
        ];
        return $questionqueryline[array_rand($questionqueryline, 1)];
    }

    public function generateSubject()
    {
        $subject = [
            'Your invoice is available for ::invoice_id',
            'Your order ::invoice_id has been received !',
            'Your order summary for order no ::invoice_id',
            'Order Alert order received for ::invoice_id',
            // 'You paid ::amount to ::organization for order ::invoice_id',
            // 'Payment of ::amount received for order ::invoice_id',
            // '::organization - Purchase transaction confirmation of ::amount',
            'Thank you for your purchase!',
            // 'Order Notification from ::organization: You have paid ::amount for order no ::invoice_id'
        ];
        return $subject[array_rand($subject, 1)];
    }
}
