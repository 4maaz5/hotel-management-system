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
        Schema::create('property_tourism_licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->index();

            $table->foreignId('branch_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('tourism_activity_type', [
                'hotel',
                'serviced_apartment',
                'camp',
                'holiday_house',
                'hostel',
                'apartment_hotel',
                'resort',
                'hotel_villa',
                'heritage_hotel',
                'pop_up_accommodation',
            ]);

            $table->string('license_number')->unique();
            $table->date('license_expiry_date');
            $table->unsignedInteger('number_of_rooms')->nullable();
            $table->unsignedInteger('number_of_beds')->nullable();
            $table->string('license_file_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_tourism_licenses');
    }
};

