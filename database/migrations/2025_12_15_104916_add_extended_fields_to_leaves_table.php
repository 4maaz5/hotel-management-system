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
        Schema::table('leaves', function (Blueprint $table) {
            $table->integer('total_days')->after('end_date');
            $table->enum('payment_type', ['paid', 'unpaid'])->default('paid')->after('total_days');
            $table->enum('travel_responsibility', ['company', 'employee'])->nullable()->after('payment_type');
            $table->decimal('ticket_amount', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropColumn(['total_days', 'payment_type', 'travel_responsibility', 'ticket_amount']);
        });
    }
};
