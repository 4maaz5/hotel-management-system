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
        Schema::create('reservation_source_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->index();
            $table->foreignId('master_source_id')
                ->constrained('reservation_source_masters')
                ->cascadeOnDelete();
            $table->boolean('status')->default(true);
            $table->string('report_name')->nullable();
            $table->string('url')->nullable();
            $table->decimal('commission_rate', 5, 2)->nullable();
            $table->enum('tax_mode', ['manual', 'auto'])
                ->default('auto');
            $table->enum('tax_calculation_type', ['inclusive', 'exclusive'])
                ->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_source_settings');
    }
};

