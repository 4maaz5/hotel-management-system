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
        Schema::table('marketing_quotations', function (Blueprint $table) {
            $table->string('vat_no')->nullable()->after('quotation_amount');
            $table->string('email')->nullable()->after('vat_no');
            $table->string('cr_no')->nullable()->after('email');
            $table->string('bank_name')->nullable()->after('cr_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marketing_quotations', function (Blueprint $table) {
            $table->dropColumn(['vat_no', 'email', 'cr_no', 'bank_name']);
        });
    }
};
