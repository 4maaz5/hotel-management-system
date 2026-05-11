<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('night_audits', function (Blueprint $table) {
            $table->json('financial_summary')->nullable()->after('notes');
        });
    }

    public function down()
    {
        Schema::table('night_audits', function (Blueprint $table) {
            $table->dropColumn('financial_summary');
        });
    }
};
