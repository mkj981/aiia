<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\EmployeeLoan;
use App\Models\Employer;
use App\Models\PayCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeLoanTest extends TestCase
{
    use RefreshDatabase;

    public function test_saving_loan_recalculates_balance_from_amounts(): void
    {
        $employer = Employer::query()->create(['name' => 'Acme Ltd']);
        $employee = Employee::query()->create(['employer_id' => $employer->id]);
        $payCodeId = PayCode::query()->create([
            'name' => 'TERMINATION',
            'description' => 'Termination Payment',
            'is_active' => true,
        ])->id;

        $loan = new EmployeeLoan([
            'employee_id' => $employee->id,
            'pay_code_id' => $payCodeId,
            'loan_amount' => '1000.00',
            'previously_paid' => '100.00',
            'period_amount' => '50.00',
            'amount_repaid' => '150.00',
            'pause_payments' => false,
        ]);
        $loan->save();

        $loan->refresh();
        $this->assertSame('750.00', (string) $loan->balance);
    }

    public function test_saving_loan_with_zero_repaid_sets_balance_to_loan_minus_previously_paid(): void
    {
        $employer = Employer::query()->create(['name' => 'Acme Ltd']);
        $employee = Employee::query()->create(['employer_id' => $employer->id]);
        $payCodeId = PayCode::query()->create([
            'name' => 'TERMINATION',
            'description' => 'Termination Payment',
            'is_active' => true,
        ])->id;

        $loan = new EmployeeLoan([
            'employee_id' => $employee->id,
            'pay_code_id' => $payCodeId,
            'loan_amount' => '500.00',
            'previously_paid' => '125.50',
            'period_amount' => '25.00',
            'amount_repaid' => '0.00',
            'pause_payments' => false,
        ]);
        $loan->save();

        $loan->refresh();
        $this->assertSame('374.50', (string) $loan->balance);
    }
}
