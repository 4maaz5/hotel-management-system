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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->foreignId('branch_id')
                ->constrained('branches')
                ->cascadeOnDelete();

            // Vehicle Info
            $table->string('name');
            $table->string('model')->nullable();
            $table->string('plate_number');

            // Owner Info
            $table->string('owner_name')->nullable();
            $table->string('owner_contact')->nullable();
            $table->string('owner_iqama')->nullable();

            $table->timestamps();
            $table->unique(['company_id', 'plate_number'], 'vehicles_company_plate_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
