<?php

return [
    'MIN_PAYROLL_START_YEAR' => 2017,
    'currency_code' => 'GBP',

    'COUNTRIES' => [
        'ENG' => 'England',
        'NIR' => 'North Ireland',
        'SCT' => 'Scotland',
        'WLS' => 'Wales',
        'other' => 'Outside of the UK',
        'GB' => 'United Kingdom',
    ],
    'ACCEPT_IMAGE_TYPE' => [
        'image/jpeg',
        'image/png',
        'image/webp',
    ],

    'countries_of_bank' => [
        'US' => 'United States',
        'GB' => 'United Kingdom',
        'CA' => 'Canada',
        'DE' => 'Germany',
        'FR' => 'France',
        'JO' => 'Jordan',
        'JP' => 'Japan',
        'AU' => 'Australia',
        'IN' => 'India',
        'BR' => 'Brazil',
    ],

    'payment_date_type' => [
        'pay_date' => 'Pay Date',
        'date_of_month' => 'Set Date of month',
    ],

    'payment_schedule' => [
        'quarterly' => 'Quarterly',
        'monthly' => 'Monthly',
    ],

    'pro_rata_adjustment' => [
        'automatic' => [
            'name' => 'Automatic',
            'description' => 'Automatically pro-rate pay for starters and leavers, and reduce pay for absences. ',
        ],
        'manual' => [
            'name' => 'Manual',
            'description' => "Don't change the values above. I will manually make any required changes",
        ],
    ],


    'Employer_student_loan' => [
        'none' => 'None',
        '1' => 'Plan One',
        '2' => 'Plan Two',
        '4' => 'Plan Four',
        '5' => 'Plan Five',
    ],

    'hours_normally_worked_band' => [
        'less_than_16' => 'Less than 16',
        'more_than_16' => 'More Than 16',
        'more_than_24' => 'More Than 24',
        'more_than_30' => 'More Than 30',
        'not_regular' => 'Not Regular',

    ],

    'payment_method' => [
        'cash' => 'Cash',
        'cheque' => 'Cheque',
        'credit' => 'Credit',
        'Direct_debit' => 'Direct debit',
    ],

    'vehicle_type' => [
        'car' => 'Car',
        'Motorcycle' => 'Motorcycle',
        'Cycle' => 'Cycle',
        'none' => 'None',
    ],
];
