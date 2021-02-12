<?php

namespace Drupal\controlpanel\API\MessageGenerator;

class MessageParameterGenertor
{
    //8-4-4-4-12 
    public function generate_uuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0C2f) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0x2Aff),
            mt_rand(0, 0xffD3),
            mt_rand(0, 0xff4B)
        );
    }

    public function generate_invid()
    {
        return sprintf(
            '%04x-%04x-%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0C2f) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000
        );
    }

    public function generate_timeline()
    {
        $timelines = [
            '48 hours',
            '72 hours',
            '2 working days',
            '3 working days',
        ];
        return $timelines[array_rand($timelines, 1)];
    }

    public function generate_order_timeline()
    {
        $timelines = [
            '6 months',
            '1 year',
            '12 months',
            '365 days',
        ];
        return $timelines[array_rand($timelines, 1)];
    }

    public function generate_random_value($min, $max)
    {
        return mt_rand($min, $max);
    }

    public function generate_amount($min = 299, $max = 499, $currency = '$')
    {
        return $currency . number_format(mt_rand($min, $max), 2, '.', ',');
    }

    public function generate_executive_name()
    {
        $name = [
            'John', 'Jordan', 'Thomas', 'Gean', 'Andromeda', 'Samarsukti', 'Watton', 'Rizzviz', 'Sophia', 'Rittoboto', 'Percy',
            'McDowel', 'Kevin', 'Banshee', 'Arthur', 'Fred', 'George'
        ];

        return $name[array_rand($name, 1)];
    }
}
