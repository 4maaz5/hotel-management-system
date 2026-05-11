<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->decimal('market_price', 15, 2)->nullable();
            $table->decimal('rent', 15, 2)->nullable();
            $table->decimal('sale_price', 15, 2)->nullable();
            $table->date('rent_start_date')->nullable();
            $table->date('rent_end_date')->nullable();
            $table->decimal('damage_assist', 15, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['market_price', 'rent', 'sale_price', 'rent_start_date', 'rent_end_date', 'damage_assist']);
        });
    }
};
