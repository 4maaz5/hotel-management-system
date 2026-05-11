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
        Schema::create('report_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->enum('naming_method', [
                'sequence',
                'year_sequence',
                'prefix_sequence',
                'prefix_year_sequence',
            ])->default('sequence');
            $table->string('prefix', 10)->nullable();
            $table->unsignedBigInteger('current_sequence')->default(1);
            $table->unsignedInteger('sequence_length')->default(5);
            $table->boolean('reset_yearly')->default(false);
            $table->year('last_reset_year')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_settings');
    }
};

