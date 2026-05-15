<?php

namespace Database\Seeders;

use App\Models\AeoType;
use App\Models\BankCsvFormat;
use App\Models\BenefitType;
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

        User::factory()->create([
            'name' => 'superadmin',
            'email' => 'superadmin@aiia.com',
            'password' => bcrypt('superadmin'),
        ]);

        $bankCsvFormat = [
            [
                'name' => 'BACS Multi',
                'description' => 'A file formated to basc standard 18 for multi payment dates',
                'requires_bacs_sun' => true,
            ],
            [
                'name' => 'Bank of America BASC',
                'description' => 'A CSV file formatted to the Bank of America BASC file format',
                'requires_bacs_sun' => true,
            ],
            [
                'name' => 'Barclays (SIF/Pegasus)',
                'description' => 'A SIF/Pegasus file formatted. An alternative format for barclays',
                'requires_bacs_sun' => false,
            ],
        ];

        BankCsvFormat::insert($bankCsvFormat);

        $sender_types = [
            [
                'name' => 'Agent',
                'description' => 'Agent',
            ],
            [
                'name' => 'Bureau',
                'description' => 'Bureau',
            ],
            [
                'name' => 'Employer',
                'description' => 'Employer',
            ],
            [
                'name' => 'Company',
                'description' => 'Company',
            ],

        ];

        SenderType::insert($sender_types);

        $now = now();

        PaySchedule::insert([
            ['name' => 'Monthly', 'description' => 'Monthly', 'shows_annual_salary' => true, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Weekly', 'description' => 'Weekly', 'shows_annual_salary' => true, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Daily', 'description' => 'Daily', 'shows_annual_salary' => true, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Fortnightly', 'description' => 'Fortnightly', 'shows_annual_salary' => true, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'FourWeekly', 'description' => 'Four Weekly', 'shows_annual_salary' => true, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Custom', 'description' => 'Custom', 'shows_annual_salary' => false, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        $scheduleIds = PaySchedule::query()
            ->whereIn('name', ['Monthly', 'Weekly', 'Daily', 'Fortnightly', 'FourWeekly', 'Custom'])
            ->pluck('id', 'name');

        $basisRow = static function (int $payScheduleId, string $name, string $description) use ($now): array {
            return [
                'pay_schedule_id' => $payScheduleId,
                'name' => $name,
                'description' => $description,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        };

        $standardBases = static function (int $payScheduleId, string $salaryDescription) use ($basisRow): array {
            return [
                $basisRow($payScheduleId, 'Hourly', 'Based on an Hourly Rate'),
                $basisRow($payScheduleId, 'Day', 'Based on a Day Rate'),
                $basisRow($payScheduleId, 'Rate/Annual', $salaryDescription),
            ];
        };

        $payBasisRows = array_merge(
            $standardBases((int) $scheduleIds['Monthly'], 'Based on a Monthly Rate/Annual Salary'),
            $standardBases((int) $scheduleIds['Weekly'], 'Based on a Weekly Rate/Annual Salary'),
            $standardBases((int) $scheduleIds['Daily'], 'Based on a Daily Rate/Annual Salary'),
            $standardBases((int) $scheduleIds['Fortnightly'], 'Based on a Fortnightly Rate/Annual Salary'),
            $standardBases((int) $scheduleIds['FourWeekly'], 'Based on a FourWeekly Rate/Annual Salary'),
            [
                $basisRow((int) $scheduleIds['Custom'], 'Hourly', 'Based on an Hourly Rate'),
                $basisRow((int) $scheduleIds['Custom'], 'Day', 'Based on a Day Rate'),
                $basisRow((int) $scheduleIds['Custom'], 'Fixed', 'Based on the same amount every period'),
            ],
        );

        PayBasis::insert($payBasisRows);

        $nis = [
            [
                'code' => 'A',
                'description' => 'All Employees not assigned to a different code',
            ],
            [
                'code' => 'B',
                'description' => 'Married women and widows entitled to pay reduced National Insurance',
            ],
            [
                'code' => 'C',
                'description' => 'Employees over the State Pension age',
            ],
            [
                'code' => 'D',
                'description' => "Employee who can defer National Insurance because they're already paying it in another job, for Investment Zones",
            ],
            [
                'code' => 'E',
                'description' => 'Married Women and Widows entitled to pay reduced National Insurance for Investment Zones',
            ],
        ];

        Ni::insert($nis);

        $aeos = [
            [
                'name' => 'AEO Maintenance',
                'description' => 'Payment of unpaid TV licence or road traffic fines.',
            ],
            [
                'name' => 'AEO Fines',
                'description' => '',
            ],
            [
                'name' => 'Child Support',
                'description' => '',
            ],
            [
                'name' => 'Child Support 2012',
                'description' => '',
            ],
            [
                'name' => 'Deduction from Earnings Child Support (DEO)',
                'description' => '',
            ],
            [
                'name' => 'AEO Civil Debts',
                'description' => 'For civil debts such as unpaid credit cards or money owed for work done.',
            ],
        ];
        AeoType::insert($aeos);

        $leavesTypes = [
            ['name' => 'Unauthorized Absence'],
            ['name' => 'Holiday'],
            ['name' => 'Sick Leave'],
            ['name' => 'Maternity Leave'],
            ['name' => 'Paternity Leave'],
            ['name' => 'Adoption Leave'],
            ['name' => 'Shared Parental Leave'],
            ['name' => 'Bereavement Leave'],
            ['name' => 'Shared Parental Leave (Adoption)'],
            ['name' => 'Paternity Leave (Adoption)'],
            ['name' => 'Strike Action'],
            ['name' => 'Neonatal Care Leave'],
            ['name' => 'Bereavement Leave (Nothern Ireland)'],
        ];

        LeavesType::insert($leavesTypes);

        $notes_type = [
            ['name' => 'General Notes'],
            ['name' => 'New starter Statement'],
            ['name' => 'Right to Work Proof'],
            ['name' => 'P45'],
        ];

        EmployeeNoteType::insert($notes_type);

        $now = now();

        BenefitType::query()->insert([
            ['code' => 'A', 'name' => 'Assets transferred', 'description' => 'Assets transferred (cars, property, goods or other assets)', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'B_PAY', 'name' => 'Payments', 'description' => 'Payments made on behalf of employee', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'B_TAX', 'name' => 'Tax', 'description' => 'Tax on notional payments', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'C', 'name' => 'Vouchers', 'description' => 'Vouchers and credit cards', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'D', 'name' => 'Living Accommodation', 'description' => 'Living Accommodation', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'E', 'name' => 'Mileage', 'description' => 'Mileage allowance and passenger payments', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'F', 'name' => 'Car', 'description' => 'Car and fuel', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'G', 'name' => 'Vans', 'description' => 'Vans and fuel', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'H', 'name' => 'Loans', 'description' => 'Interest-free or low interest loans', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'I', 'name' => 'Medical', 'description' => 'Private medical treatment or insurance', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'J', 'name' => 'Qualifying Relocation Expenses', 'description' => 'Qualifying relocation expenses payments and benefits', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'K', 'name' => 'Services', 'description' => 'Services supplied', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'L', 'name' => 'Use of Assets', 'description' => "Assets placed at the employee's disposal", 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'M_1A', 'name' => 'Other items (Class 1A)', 'description' => 'Other items (Class 1A)', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'M_NON', 'name' => 'Other items (Non-Class 1A)', 'description' => 'Other items (Non-Class 1A)', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'M_DIR', 'name' => 'Directors Income Tax', 'description' => "Income Tax paid but not deducted from director's remuneration", 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'N_TRV', 'name' => 'Travel', 'description' => 'Travelling and subsistence payments', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'N_ENT', 'name' => 'Entertainment', 'description' => 'Entertainment', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'N_TEL', 'name' => 'Telephone', 'description' => 'Payments for use of home telephone', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'N_RLOC', 'name' => 'Non-qualifying relocation expenses', 'description' => 'Non-qualifying relocation expenses', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
