<?php

namespace Database\Seeders;

use App\Models\AeoType;
use App\Models\BankCsvFormat;
use App\Models\EmployeeNoteType;
use App\Models\LeavesType;
use App\Models\Ni;
use App\Models\PayBasis;
use App\Models\PaySchedule;
use App\Models\SenderType;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        //                User::factory()->create([
        //                    'name' => 'superadmin',
        //                    'email' => 'superadmin@aiia.com',
        //                    'password' => bcrypt('superadmin'),
        //                ]);
        //
        //                $bankCsvFormat = [
        //                    [
        //                        'name' => 'BACS Multi',
        //                        'description' => 'A file formated to basc standard 18 for multi payment dates',
        //                        'requires_bacs_sun' => true,
        //                    ],
        //                    [
        //                        'name' => 'Bank of America BASC',
        //                        'description' => 'A CSV file formatted to the Bank of America BASC file format',
        //                        'requires_bacs_sun' => true,
        //                    ],
        //                    [
        //                        'name' => 'Barclays (SIF/Pegasus)',
        //                        'description' => 'A SIF/Pegasus file formatted. An alternative format for barclays',
        //                        'requires_bacs_sun' => false,
        //                    ],
        //                ];
        //
        //                BankCsvFormat::insert($bankCsvFormat);
        //
        //
        //                $sender_types = [
        //                    [
        //                        'name' => 'Agent',
        //                        'description' => 'Agent',
        //                    ],
        //                    [
        //                        'name' => 'Bureau',
        //                        'description' => 'Bureau',
        //                    ],
        //                    [
        //                        'name' => 'Employer',
        //                        'description' => 'Employer',
        //                    ],
        //                    [
        //                        'name' => 'Company',
        //                        'description' => 'Company',
        //                    ],
        //
        //                ];
        //
        //                SenderType::insert($sender_types);
        //
        //        $pay_schedules = [
        //            [
        //                'name' => 'Monthly',
        //                'shows_annual_salary' => true,
        //            ],
        //            [
        //                'name' => 'P 2New Schedule',
        //                'shows_annual_salary' => true,
        //            ],
        //            [
        //                'name' => 'Custom',
        //                'shows_annual_salary' => false,
        //            ],
        //            [
        //                'name' => 'FourWeekly Amount',
        //                'shows_annual_salary' => true,
        //            ],
        //            [
        //                'name' => 'Fortnightly',
        //                'shows_annual_salary' => true,
        //            ],
        //            [
        //                'name' => 'Weekly',
        //                'shows_annual_salary' => true,
        //            ],
        //            [
        //                'name' => 'Daily',
        //                'shows_annual_salary' => true,
        //            ],
        //        ];
        //        PaySchedule::insert($pay_schedules);
        //
        //        $pay_basis = [
        //            [
        //                'pay_schedule_id' => 1,
        //                'name' => 'Hourly',
        //                'description' => 'Based on an Hourly Rate',
        //            ],
        //            [
        //                'pay_schedule_id' => 1,
        //                'name' => 'Day',
        //                'description' => 'Based on a Day Rate',
        //            ],
        //            [
        //                'pay_schedule_id' => 1,
        //                'name' => 'Rate/Annual',
        //                'description' => 'Based on a Monthly Rate/Annual Salary',
        //            ],
        //
        //            [
        //                'pay_schedule_id' => 2,
        //                'name' => 'Hourly',
        //                'description' => 'Based on an Hourly Rate',
        //            ],
        //            [
        //                'pay_schedule_id' => 2,
        //                'name' => 'Day',
        //                'description' => 'Based on a Day Rate',
        //            ],
        //            [
        //                'pay_schedule_id' => 2,
        //                'name' => 'Rate/Annual',
        //                'description' => 'Based on a Monthly Rate/Annual Salary',
        //            ],
        //
        //            [
        //                'pay_schedule_id' => 3,
        //                'name' => 'Hourly',
        //                'description' => 'Based on an Hourly Rate',
        //            ],
        //            [
        //                'pay_schedule_id' => 3,
        //                'name' => 'Day',
        //                'description' => 'Based on a Day Rate',
        //            ],
        //            [
        //                'pay_schedule_id' => 3,
        //                'name' => 'Day',
        //                'description' => 'Based on a same amount every period',
        //            ],
        //
        //            [
        //                'pay_schedule_id' => 4,
        //                'name' => 'Hourly',
        //                'description' => 'Based on an Hourly Rate',
        //            ],
        //            [
        //                'pay_schedule_id' => 4,
        //                'name' => 'Day',
        //                'description' => 'Based on a Day Rate',
        //            ],
        //            [
        //                'pay_schedule_id' => 4,
        //                'name' => 'Day',
        //                'description' => 'Based on a FourWeekly Rate/Annual Salary',
        //            ],
        //
        //            [
        //                'pay_schedule_id' => 5,
        //                'name' => 'Hourly',
        //                'description' => 'Based on an Hourly Rate',
        //            ],
        //            [
        //                'pay_schedule_id' => 5,
        //                'name' => 'Day',
        //                'description' => 'Based on a Day Rate',
        //            ],
        //            [
        //                'pay_schedule_id' => 5,
        //                'name' => 'Day',
        //                'description' => 'Based on a Fortnightly Rate/Annual Salary',
        //            ],
        //
        //            [
        //                'pay_schedule_id' => 6,
        //                'name' => 'Hourly',
        //                'description' => 'Based on an Hourly Rate',
        //            ],
        //            [
        //                'pay_schedule_id' => 6,
        //                'name' => 'Day',
        //                'description' => 'Based on a Day Rate',
        //            ],
        //            [
        //                'pay_schedule_id' => 6,
        //                'name' => 'Day',
        //                'description' => 'Based on a Weekly Rate/Annual Salary',
        //            ],
        //
        //            [
        //                'pay_schedule_id' => 7,
        //                'name' => 'Hourly',
        //                'description' => 'Based on an Hourly Rate',
        //            ],
        //            [
        //                'pay_schedule_id' => 7,
        //                'name' => 'Day',
        //                'description' => 'Based on a Day Rate',
        //            ],
        //            [
        //                'pay_schedule_id' => 7,
        //                'name' => 'Day',
        //                'description' => 'Based on a Daily Rate/Annual Salary',
        //            ],
        //        ];
        //        PayBasis::insert($pay_basis);
        //
        //
        //        $nis = [
        //            [
        //              'code' => 'A',
        //              'description' => 'All Employees not assigned to a different code'
        //            ],
        //            [
        //                'code' => 'B',
        //                'description' => 'Married women and widows entitled to pay reduced National Insurance'
        //            ],
        //            [
        //                'code' => 'C',
        //                'description' => 'Employees over the State Pension age'
        //            ],
        //            [
        //                'code' => 'D',
        //                'description' => "Employee who can defer National Insurance because they're already paying it in another job, for Investment Zones"
        //            ],
        //            [
        //                'code' => 'E',
        //                'description' => 'Married Women and Widows entitled to pay reduced National Insurance for Investment Zones'
        //            ],
        //        ];
        //
        //        Ni::insert($nis);

        //        $aeos = [
        //            [
        //                'name' => 'AEO Maintenance',
        //                'description' => 'Payment of unpaid TV licence or road traffic fines.',
        //            ],
        //            [
        //                'name' => 'AEO Fines',
        //                'description' => '',
        //            ],
        //            [
        //                'name' => 'Child Support',
        //                'description' => '',
        //            ],
        //            [
        //                'name' => 'Child Support 2012',
        //                'description' => '',
        //            ],
        //            [
        //                'name' => 'Deduction from Earnings Child Support (DEO)',
        //                'description' => '',
        //            ],
        //            [
        //                'name' => 'AEO Civil Debts',
        //                'description' => 'For civil debts such as unpaid credit cards or money owed for work done.',
        //            ],
        //        ];
        //        AeoType::insert($aeos);

        //        $leavesTypes = [
        //            ['name' => 'Unauthorized Absence'],
        //            ['name' => 'Holiday'],
        //            ['name' => 'Sick Leave'],
        //            ['name' => 'Maternity Leave'],
        //            ['name' => 'Paternity Leave'],
        //            ['name' => 'Adoption Leave'],
        //            ['name' => 'Shared Parental Leave'],
        //            ['name' => 'Bereavement Leave'],
        //            ['name' => 'Shared Parental Leave (Adoption)'],
        //            ['name' => 'Paternity Leave (Adoption)'],
        //            ['name' => 'Strike Action'],
        //            ['name' => 'Neonatal Care Leave'],
        //            ['name' => 'Bereavement Leave (Nothern Ireland)'],
        //        ];
        //
        //        LeavesType::insert($leavesTypes);

        $notes_type = [
            ['name' => 'General Notes'],
            ['name' => 'New starter Statement'],
            ['name' => 'Right to Work Proof'],
            ['name' => 'P45'],
        ];

        EmployeeNoteType::insert($notes_type);
    }
}
