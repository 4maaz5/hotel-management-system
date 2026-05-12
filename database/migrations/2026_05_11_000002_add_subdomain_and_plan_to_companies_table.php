<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('subdomain')->unique()->nullable()->after('name');
            $table->foreignId('subscription_plan_id')->nullable()->constrained('subscription_plans')->nullOnDelete()->after('subscription_status');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['subscription_plan_id']);
            $table->dropColumn(['subdomain', 'subscription_plan_id']);
        });
    }
};
