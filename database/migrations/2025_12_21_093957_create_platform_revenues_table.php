<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_revenues', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subscription_id')
                ->constrained('platform_subscriptions')
                ->cascadeOnDelete();

            $table->decimal('amount_collected', 15, 2);
            $table->decimal('commission_amount', 15, 2);

            $table->date('payment_date');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_revenues');
    }
};
