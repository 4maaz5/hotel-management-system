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
        Schema::create('unit_custom_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->index();

            $table->foreignId('unit_id')
                ->constrained('units')
                ->cascadeOnDelete();

            $table->foreignId('unit_type_id')
                ->constrained('unit_types')
                ->cascadeOnDelete();

            $table->decimal('low_weekday_rate', 10, 2)->nullable();
            $table->decimal('high_weekday_rate', 10, 2)->nullable();
            $table->decimal('daily_min_rate', 10, 2)->nullable();

            $table->decimal('monthly_rate', 10, 2)->nullable();
            $table->decimal('monthly_min_rate', 10, 2)->nullable();

            $table->timestamps();

            $table->unique('unit_id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_custom_rates');
    }
};

