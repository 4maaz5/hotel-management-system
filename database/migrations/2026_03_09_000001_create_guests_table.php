<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->index();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('first_name');
            $table->string('second_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->foreignId('guest_class_id')->nullable()->constrained('guest_classes')->onDelete('set null');
            $table->string('nationality', 10)->nullable();
            $table->string('nationality_code', 3)->nullable();
            $table->enum('guest_type', ['individual', 'family', 'corporate'])->nullable();
            $table->enum('id_type', ['national_id', 'passport', 'iqama', 'driver_license'])->nullable();
            $table->string('id_number', 50)->nullable();
            $table->string('id_issue_country', 10)->nullable();
            $table->date('id_expiry_date')->nullable();
            $table->string('visa_number', 50)->nullable();
            $table->string('arrival_from')->nullable();
            $table->enum('id_serial', ['first', 'second', 'third', 'last'])->nullable();
            $table->string('mobile_dial_code', 10)->nullable();
            $table->string('mobile_number', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('work_place')->nullable();
            $table->string('work_phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('car_license_plate', 50)->nullable();
            $table->string('profile_image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('guests');
    }
};

