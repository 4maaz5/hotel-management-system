<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ntmp_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->boolean('enabled')->default(false);
            $table->string('mode', 20)->default('simulation');
            $table->string('driver', 20)->default('fake');
            $table->string('provider_name')->nullable();
            $table->string('endpoint')->nullable();
            $table->text('api_key')->nullable();
            $table->string('username')->nullable();
            $table->text('password')->nullable();
            $table->string('branch_reference')->nullable();
            $table->string('license_reference')->nullable();
            $table->string('establishment_reference')->nullable();
            $table->string('connection_status', 30)->default('not_connected');
            $table->timestamp('last_synced_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'branch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ntmp_settings');
    }
};
