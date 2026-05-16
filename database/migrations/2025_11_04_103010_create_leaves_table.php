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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->enum('leave_type', ['sick', 'annual', 'maternity', 'emergency', 'unpaid', 'paternity', 'compensatory', 'bereavement']);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_days');
            $table->enum('payment_type', ['paid', 'unpaid'])->default('paid');
            $table->enum('travel_responsibility', ['company', 'employee'])->nullable();
            $table->decimal('ticket_amount', 10, 2)->nullable();
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'in_progress'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
