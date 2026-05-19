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
        Schema::create('unit_type_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->index();

            $table->foreignId('unit_type_id')
                ->constrained('unit_types')
                ->cascadeOnDelete();

            // Daily Rates
            $table->decimal('low_weekday_rate', 10, 2)->default(0);
            $table->decimal('high_weekday_rate', 10, 2)->default(0);
            $table->decimal('daily_min_rate', 10, 2)->default(0);

            // Monthly Rates
            $table->decimal('monthly_rate', 10, 2)->default(0);
            $table->decimal('monthly_min_rate', 10, 2)->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['company_id', 'unit_type_id'], 'unit_type_rates_company_type_unique');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_type_rates');
    }
};
