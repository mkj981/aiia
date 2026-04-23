<?php

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
        Schema::create('rtis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained()->cascadeOnDelete()->unique();
            $table->foreignId('sender_type_id')->nullable()->constrained('sender_types')->nullOnDelete();

            $table->string('govt_gateway_id', 100)->nullable();
            $table->string('password', 255)->nullable();

            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('phone', 30)->nullable();

            $table->boolean('auto_submit_fps_after_finalising_pay_run')->default(false);
            $table->boolean('include_employees_with_no_payment_on_fps')->default(false);
            $table->boolean('test_mode')->default(false);
            $table->boolean('use_test_gateway')->default(false);
            $table->boolean('allow_linked_eps')->default(false);
            $table->boolean('compress_fps')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rtis');
    }
};
