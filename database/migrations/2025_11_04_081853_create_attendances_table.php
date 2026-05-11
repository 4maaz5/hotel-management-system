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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            // Foreign Key to employees table
            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->onDelete('cascade');

            // Attendance fields
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();

            $table->enum('status', ['Present', 'Absent', 'Leave'])->default('Present');
            $table->decimal('overtime_hours', 5, 2)->default(0);

            $table->timestamps();

            // Prevent duplicate attendance for same employee on same date
            $table->unique(['employee_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
