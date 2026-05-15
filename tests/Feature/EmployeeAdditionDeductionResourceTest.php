<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\EmployeeAdditionDeduction;
use App\Models\Employer;
use App\Models\PayCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeAdditionDeductionResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_hourly_pay_code_stores_computed_period_total_from_quantity_and_rate(): void
    {
        $employer = Employer::query()->create(['name' => 'Acme Ltd']);
        $employee = Employee::query()->create(['employer_id' => $employer->id]);
        $payCodeId = PayCode::query()->where('name', 'BASICHOURLY')->value('id');
        $this->assertNotNull($payCodeId);

        $line = new EmployeeAdditionDeduction([
            'employee_id' => $employee->id,
            'pay_code_id' => $payCodeId,
            'quantity' => 10,
            'rate' => 2.5,
            'description' => 'Basic Hourly Pay',
        ]);
        $line->save();

        $line->refresh();
        $this->assertEquals(25.0, (float) $line->fixed_period_amount);
        $this->assertNull($line->fixed_annual_amount);
        $this->assertFalse($line->gross_up_target_net);
        $this->assertNull($line->pro_rata_adjustment);
    }

    public function test_fixed_period_pay_code_clears_quantity_rate_and_annual_fields(): void
    {
        $employer = Employer::query()->create(['name' => 'Acme Ltd']);
        $employee = Employee::query()->create(['employer_id' => $employer->id]);
        $payCodeId = PayCode::query()->where('name', 'BASIC')->value('id');
        $this->assertNotNull($payCodeId);

        $line = new EmployeeAdditionDeduction([
            'employee_id' => $employee->id,
            'pay_code_id' => $payCodeId,
            'fixed_period_amount' => 150,
            'fixed_annual_amount' => 99,
            'full_time_annual_value' => 88,
            'quantity' => 5,
            'rate' => 2,
            'gross_up_target_net' => true,
            'pro_rata_adjustment' => 'manual',
            'description' => 'Basic Pay',
        ]);
        $line->save();

        $line->refresh();
        $this->assertEquals(150.0, (float) $line->fixed_period_amount);
        $this->assertNull($line->fixed_annual_amount);
        $this->assertNull($line->full_time_annual_value);
        $this->assertNull($line->quantity);
        $this->assertNull($line->rate);
        $this->assertTrue($line->gross_up_target_net);
        $this->assertSame('manual', $line->pro_rata_adjustment);
    }
}
