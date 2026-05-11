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
        Schema::create('seasonal_unit_type_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->index();

            $table->foreignId('seasonal_rate_id')
                ->constrained('seasonal_rates')
                ->cascadeOnDelete();

            $table->foreignId('unit_type_id')
                ->constrained('unit_types')
                ->cascadeOnDelete();

            $table->decimal('low_weekday_rate', 10, 2)->nullable();
            $table->decimal('high_weekday_rate', 10, 2)->nullable();
            $table->decimal('daily_min_rate', 10, 2)->nullable();

            $table->timestamps();

            $table->unique(['seasonal_rate_id', 'unit_type_id']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seasonal_unit_type_rates');
    }
};

