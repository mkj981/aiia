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

    /**
     * Ordered list of pay code `name` values shown on the employee addition/deduction line form.
     *
     * @var list<string>
     */
    'employee_addition_deduction_pay_code_order' => [
        'BASIC',
        'BASICANNUAL',
        'BASICHOURLY',
        'BASICDAILY',
        'AHPAY',
        'AHPAYHRS',
        'AHPAYHRSLVR',
        'HOLDAYRATE',
        'TERMINATION',
    ],

    /**
     * Ordered list of pay code `name` values shown on the employee loan form.
     *
     * @var list<string>
     */
    'employee_loan_pay_code_order' => [
        'TERMINATION',
        'BASIC',
        'BASICANNUAL',
        'BASICHOURLY',
        'BASICDAILY',
        'AHPAY',
        'AHPAYHRS',
        'AHPAYHRSLVR',
        'HOLDAYRATE',
    ],

    /**
     * Maps pay code `name` to employee addition/deduction form behaviour.
     *
     * @var array<string, string>
     */
    'employee_addition_deduction_line_kinds' => [
        'BASIC' => 'fixed_period',
        'AHPAY' => 'fixed_period',
        'TERMINATION' => 'fixed_period',
        'BASICANNUAL' => 'fixed_annual',
        'BASICHOURLY' => 'hourly',
        'AHPAYHRS' => 'hourly',
        'AHPAYHRSLVR' => 'hourly',
        'BASICDAILY' => 'daily',
        'HOLDAYRATE' => 'daily',
    ],

    /**
     * Seed data for `pay_codes` used by employee addition/deduction lines (name => description).
     *
     * @var array<string, string>
     */
    'employee_addition_deduction_pay_code_definitions' => [
        'BASIC' => 'Basic Pay',
        'BASICANNUAL' => 'Basic Pay',
        'BASICHOURLY' => 'Basic Hourly Pay',
        'BASICDAILY' => 'Basic Daily Pay',
        'AHPAY' => 'Accrued Holiday Pay',
        'AHPAYHRS' => 'Accrued Holiday Pay Hours',
        'AHPAYHRSLVR' => 'Accrued Holiday Pay Hours Leavers',
        'HOLDAYRATE' => 'Holiday Days (Day Rate)',
        'TERMINATION' => 'Termination Payment',
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

    'marital_status' => [
        'single' => 'Single',
        'married' => 'Married',
        'divorced' => 'Divorced',
        'widowed' => 'Widowed',
        'civil_partnership' => 'Civil Partnership',
        'unknown' => 'Unknown',
    ],

    'gender' => [
        'male' => 'Male',
        'female' => 'Female',
    ],

    'employment_declaration' => [
        'A' => "This is my first job since last 6 April and I have not been receiving taxable Jobseeker's Allowance, Employment and Support Allowance, taxable  Incapacity Benefit,State or Occupational Pension.",
        'B' => 'This is now my only job but since last 6 April I have had another job, or received taxable Jobseeker’s Allowance, Employment and Support Allowance , or taxable Incapacity Benefit. I do not receive a State or Occupational Pension.',
        'C' => 'As well as my new job, I have another job or receive a State or Occupational Pension.',
        'Unknown' => 'The status of the new employee is not known.',
    ],

    'employment_change_of_payroll_id' => [
        'auto' => [
            'label' => 'Detect Automatically',
            'requires_previous_code' => false,
        ],
        'force' => [
            'label' => 'Force reporting of changed ID on next FPS',
            'requires_previous_code' => true,
        ],
    ],

    'employee_pension' => [
        'does_not_work_in_the_uk' => 'Does Not Work In The UK',
        'works_in_the_uk' => 'Works in The UK',
        'ordinarily_work_in_th_uk' => 'Ordinarily Work In The UK',
    ],

    /**
     * Tax years offered when recording an employee benefit.
     *
     * @var array<string, string>
     */
    'employee_benefit_tax_years' => [
        '2026/27' => '2026/27',
        '2025/26' => '2025/26',
        '2024/25' => '2024/25',
        '2023/24' => '2023/24',
    ],

    /**
     * Employee benefit declaration types (stored value => label + helper text for the form).
     *
     * @var array<string, array{label: string, description: string}>
     */
    'employee_benefit_declaration_types' => [
        'P11D' => [
            'label' => 'P11D',
            'description' => 'Declare on a P11D after year end',
        ],
        'PAYE' => [
            'label' => 'PAYE',
            'description' => 'Declare on FPS with payroll submissions',
        ],
    ],

    /**
     * Pro-rata adjustment options for regular pay and addition/deduction lines.
     *
     * @var array<string, array{name: string, description: string}>
     */
    'pro_rata_adjustment' => [
        'automatic' => [
            'name' => 'Automatic',
            'description' => 'Automatically pro-rate pay for starters and leavers, and reduce pay for absences.',
        ],
        'manual' => [
            'name' => 'Manual',
            'description' => "Don't change the values above. I will manually make any required changes",
        ],
    ],

];
