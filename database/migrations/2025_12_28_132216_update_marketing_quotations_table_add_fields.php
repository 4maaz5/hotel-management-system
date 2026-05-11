<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketing_quotations', function (Blueprint $table) {
            // Make marketing_agent_id nullable
            $table->foreignId('marketing_agent_id')
                ->nullable()
                ->change();

            // Add new fields
            $table->string('manual_agent_name')->nullable()->after('marketing_agent_id');
            $table->string('account_number')->nullable()->after('manual_agent_name');
            $table->string('logo')->nullable()->after('account_number');
        });
    }

    public function down(): void
    {
        Schema::table('marketing_quotations', function (Blueprint $table) {
            // Revert marketing_agent_id to required
            $table->foreignId('marketing_agent_id')
                ->nullable(false)
                ->change();

            // Drop the new columns
            $table->dropColumn(['manual_agent_name', 'account_number', 'logo']);
        });
    }
};
