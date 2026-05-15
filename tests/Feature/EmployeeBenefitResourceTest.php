<?php

namespace Tests\Feature;

use App\Models\BenefitType;
use App\Models\Employee;
use App\Models\EmployeeBenefit;
use App\Models\Employer;
use Database\Seeders\BenefitTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeBenefitResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_benefit_stores_benefit_type_id_and_declaration_fields(): void
    {
        $this->seed(BenefitTypeSeeder::class);

        $employer = Employer::query()->create(['name' => 'Acme Ltd']);
        $employee = Employee::query()->create(['employer_id' => $employer->id]);

        $benefitType = BenefitType::query()->where('code', 'F')->first();
        $this->assertNotNull($benefitType);

        $benefit = EmployeeBenefit::query()->create([
            'employee_id' => $employee->id,
            'description' => 'Company car provision',
            'tax_year' => '2024/25',
            'declaration_type' => 'P11D',
            'benefit_type_id' => $benefitType->id,
        ]);

        $benefit->refresh();
        $this->assertSame($benefitType->id, $benefit->benefit_type_id);
        $this->assertSame('2024/25', $benefit->tax_year);
        $this->assertSame('P11D', $benefit->declaration_type);
        $this->assertSame('F', $benefitType->section_letter);
    }
}
