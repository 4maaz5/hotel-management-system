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
        Schema::create('outlet_setups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->index();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->boolean('status')->default(true);
            $table->string('operating_status')->index();
            $table->string('outlet_code', 3);
            $table->string('name', 200);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'branch_id', 'outlet_code'], 'outlet_setups_company_branch_code_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outlet_setups');
    }
};
