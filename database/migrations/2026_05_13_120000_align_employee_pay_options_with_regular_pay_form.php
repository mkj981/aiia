<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('employee_pay_options')) {
            return;
        }

        Schema::table('employee_pay_options', function (Blueprint $table) {
            if (Schema::hasColumn('employee_pay_options', 'monthly_amount')) {
                $table->dropColumn('monthly_amount');
            }

            if (! Schema::hasColumn('employee_pay_options', 'period_amount')) {
                $table->decimal('period_amount', 12, 2)->nullable()->after('working_pattern');
            }

            if (! Schema::hasColumn('employee_pay_options', 'hourly_rate')) {
                $table->decimal('hourly_rate', 12, 2)->nullable()->after('annual_salary');
            }

            if (! Schema::hasColumn('employee_pay_options', 'hours_in_period')) {
                $table->decimal('hours_in_period', 12, 2)->nullable()->after('hourly_rate');
            }

            if (! Schema::hasColumn('employee_pay_options', 'day_rate')) {
                $table->decimal('day_rate', 12, 2)->nullable()->after('hours_in_period');
            }

            if (! Schema::hasColumn('employee_pay_options', 'days_in_period')) {
                $table->decimal('days_in_period', 12, 2)->nullable()->after('day_rate');
            }

            if (! Schema::hasColumn('employee_pay_options', 'period_total')) {
                $table->decimal('period_total', 12, 2)->nullable()->after('days_in_period');
            }

            if (! Schema::hasColumn('employee_pay_options', 'minimum_wage')) {
                $table->boolean('minimum_wage')->default(false)->after('period_total');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('employee_pay_options')) {
            return;
        }

        Schema::table('employee_pay_options', function (Blueprint $table) {
            $columns = [
                'period_amount',
                'hourly_rate',
                'hours_in_period',
                'day_rate',
                'days_in_period',
                'period_total',
                'minimum_wage',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('employee_pay_options', $column)) {
                    $table->dropColumn($column);
                }
            }

            if (! Schema::hasColumn('employee_pay_options', 'monthly_amount')) {
                $table->decimal('monthly_amount', 12, 2)->nullable()->after('working_pattern');
            }
        });
    }
};
