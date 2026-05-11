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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            // Basic Info
            $table->string('name');
            $table->string('legal_name')->nullable();
            $table->string('logo')->nullable();

            // Registration Details (Saudi Arabia required fields)
            $table->string('cr_number')->nullable();
            $table->date('cr_expiry')->nullable();
            $table->string('vat_number')->nullable();
            $table->string('tax_card_number')->nullable();
            $table->string('establishment_id')->nullable();

            // Contact Info
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();

            // Address Info
            $table->string('country')->default('Saudi Arabia');
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('street')->nullable();
            $table->string('zip_code')->nullable();

            // Additional Settings
            $table->string('industry_type')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
