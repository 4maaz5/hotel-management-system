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
        Schema::create('printing_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->index();
            $table->string('report_key');
            $table->string('report_name');
            $table->boolean('enabled')->default(true);
            $table->boolean('letter_head')->default(false);
            $table->boolean('blank_paper')->default(false);
            $table->boolean('cashier_paper')->default(false);
            $table->string('contract_template_type')->default('double');
            $table->timestamps();

            $table->unique(['company_id', 'report_key'], 'printing_options_company_report_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('printing_options');
    }
};
