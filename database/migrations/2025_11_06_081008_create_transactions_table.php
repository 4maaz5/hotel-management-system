<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // Type of transaction (e.g., payroll, expense, bonus)
            $table->string('type', 50);

            // Amount
            $table->decimal('amount', 12, 2);

            // Transaction date
            $table->date('date');

            // Description
            $table->text('description')->nullable();

            // Branch ID
            $table->unsignedBigInteger('branch_id')->nullable();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
