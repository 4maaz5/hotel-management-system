<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->index();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('housekeeper_id')->nullable()->constrained('housekeepers')->nullOnDelete();
            $table->date('attendance_date')->index();
            $table->timestamp('check_in_at')->nullable();
            $table->decimal('check_in_latitude', 10, 8)->nullable();
            $table->decimal('check_in_longitude', 11, 8)->nullable();
            $table->unsignedInteger('check_in_accuracy_meters')->nullable();
            $table->unsignedInteger('check_in_distance_meters')->nullable();
            $table->boolean('check_in_within_geofence')->nullable();
            $table->timestamp('check_out_at')->nullable();
            $table->decimal('check_out_latitude', 10, 8)->nullable();
            $table->decimal('check_out_longitude', 11, 8)->nullable();
            $table->unsignedInteger('check_out_accuracy_meters')->nullable();
            $table->unsignedInteger('check_out_distance_meters')->nullable();
            $table->boolean('check_out_within_geofence')->nullable();
            $table->enum('status', ['checked_in', 'checked_out', 'adjusted'])->default('checked_in');
            $table->text('notes')->nullable();
            $table->foreignId('edited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'user_id', 'attendance_date']);
            $table->index(['housekeeper_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_attendances');
    }
};
