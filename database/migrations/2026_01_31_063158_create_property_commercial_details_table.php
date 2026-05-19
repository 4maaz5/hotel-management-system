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
        Schema::create('property_commercial_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->index();

            $table->foreignId('branch_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('registration_number');
            $table->string('activity_license_number')->nullable();
            $table->string('vat_registration_number')->nullable();
            $table->string('registration_file_path')->nullable();

            $table->timestamps();

            $table->unique(['company_id', 'registration_number'], 'pcd_company_registration_unique');
            $table->unique(['company_id', 'branch_id'], 'pcd_company_branch_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_commercial_details');
    }
};
