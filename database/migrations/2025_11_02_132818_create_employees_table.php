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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            $table->string('first_name');
            $table->string('last_name');
            $table->string('employee_id')->unique();

            $table->string('email')->unique()->nullable();
            $table->string('phone', 20)->nullable();

            $table->string('designation')->nullable();
            $table->string('qr_code')->unique()->nullable();

            $table->foreignId('company_id')
                ->nullable()
                ->constrained('companies')
                ->nullOnDelete();
            $table->foreignId('brand_id')
                ->nullable()
                ->constrained('brands')
                ->nullOnDelete();
            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            // Employment Info
            $table->date('join_date')->nullable();
            $table->date('residence_expiry_date')->nullable();

            // Banking Info
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('image')->nullable()->comment('Employee profile image path');
            $table->decimal('base_salary', 10, 2)->nullable();
            $table->enum('salary_type', ['monthly', 'weekly', 'daily', 'hourly'])->default('monthly');
            $table->boolean('is_commission')->default(false);
            $table->decimal('commission_percentage', 5, 2)->nullable();
            $table->enum('commission_type', ['sales', 'profit', 'revenue'])->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
