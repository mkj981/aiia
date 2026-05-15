<?php

use App\Models\PayCode;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employee_addition_deductions', function (Blueprint $table) {
            $table->decimal('fixed_annual_amount', 12, 2)->nullable()->after('fixed_period_amount');
            $table->decimal('full_time_annual_value', 12, 2)->nullable()->after('fixed_annual_amount');
            $table->decimal('quantity', 12, 2)->nullable()->after('full_time_annual_value');
            $table->decimal('rate', 12, 2)->nullable()->after('quantity');
        });

        foreach (config('general.employee_addition_deduction_pay_code_definitions') as $name => $description) {
            PayCode::query()->updateOrCreate(
                ['name' => $name],
                ['description' => $description, 'is_active' => true],
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_addition_deductions', function (Blueprint $table) {
            $table->dropColumn([
                'fixed_annual_amount',
                'full_time_annual_value',
                'quantity',
                'rate',
            ]);
        });
    }
};
