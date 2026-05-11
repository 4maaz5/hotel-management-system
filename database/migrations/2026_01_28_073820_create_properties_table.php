<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->uuid('uuid')->unique();

            $table->enum('status', ['ACTIVE', 'INACTIVE', 'SUSPENDED'])->default('ACTIVE');
            $table->string('property_name_en');
            $table->string('property_name_ar');
            $table->string('report_name_en');
            $table->string('report_name_ar');
            $table->string('property_code')->unique();
            $table->foreignId('property_type_id')->nullable()->constrained('property_types')->nullOnDelete();
            $table->foreignId('owner_user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('logo_url', 500)->nullable();
            $table->enum('account_version', ['BASIC', 'PREMIUM', 'ENTERPRISE'])->default('BASIC');
            $table->date('account_expiry_date')->nullable();

            // Location foreign keys
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignId('region_id')->nullable()->constrained('regions')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete();

            // Address
            $table->text('address_en')->nullable();
            $table->text('address_ar')->nullable();
            $table->string('building_no', 50)->nullable();
            $table->string('secondary_no', 50)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('po_box', 20)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('time_zone')->nullable();

            // Contact
            $table->string('phone', 50)->nullable();
            $table->string('mobile', 50)->nullable();
            $table->string('fax_number', 50)->nullable();
            $table->string('hot_line', 50)->nullable();
            $table->string('admin_number', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('website', 500)->nullable();

            // Counts
            $table->unsignedInteger('active_units_count')->default(0);
            $table->unsignedInteger('max_units_count')->default(0);

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('property_code');
            $table->index('status');
            $table->index('property_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};

