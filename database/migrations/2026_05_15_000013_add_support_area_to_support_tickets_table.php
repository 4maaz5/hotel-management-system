<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->string('support_area', 30)->default('reservation')->after('category');
            $table->index(['company_id', 'support_area', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropIndex(['company_id', 'support_area', 'status']);
            $table->dropColumn('support_area');
        });
    }
};
