<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('location');
            $table->string('manager');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->decimal('market_price', 15, 2)->nullable();
            $table->decimal('rent', 15, 2)->nullable();
            $table->decimal('sale_price', 15, 2)->nullable();
            $table->date('rent_start_date')->nullable();
            $table->date('rent_end_date')->nullable();
            $table->decimal('damage_assist', 15, 2)->nullable();
            $table->enum('building_type', ['owned', 'rented'])->default('owned');
            $table->decimal('total_rent', 15, 2)->nullable();
            $table->integer('installments')->nullable();
            $table->string('rent_agreement')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'name'], 'branches_company_name_unique');
            $table->unique(['company_id', 'email'], 'branches_company_email_unique');
            $table->unique(['company_id', 'phone'], 'branches_company_phone_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
