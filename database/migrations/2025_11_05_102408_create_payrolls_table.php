<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();

            // Employee relation
            $table->foreignId('employee_id')
                ->constrained('employees')
                ->onDelete('cascade');

            // Payroll info
            $table->string('month', 7);
            $table->decimal('basic_salary', 10, 2)->default(0);
            $table->decimal('allowance', 10, 2)->default(0);
            $table->decimal('deductions', 10, 2)->default(0);
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->decimal('net_pay', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->enum('status', ['Pending', 'Paid', 'Cancelled'])->default('Pending'); // removed ->after('net_pay')

            $table->timestamps();

            $table->unique(['employee_id', 'month'], 'unique_employee_month');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
