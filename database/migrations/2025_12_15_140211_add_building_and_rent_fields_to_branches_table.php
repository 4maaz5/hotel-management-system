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
        Schema::table('branches', function (Blueprint $table) {
            $table->enum('building_type', ['owned', 'rented'])->default('owned')->after('damage_assist');
            $table->decimal('total_rent', 15, 2)->nullable()->after('building_type');
            $table->integer('installments')->nullable()->after('total_rent');
            $table->string('rent_agreement')->nullable()->after('installments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['building_type', 'total_rent', 'installments', 'rent_agreement']);
        });
    }
};
