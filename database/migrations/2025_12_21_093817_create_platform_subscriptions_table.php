<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_subscriptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('third_party_platform_id')
                ->constrained('third_party_platforms')
                ->cascadeOnDelete();

            $table->foreignId('company_id')
                ->nullable()
                ->constrained('companies')
                ->nullOnDelete();

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->date('subscription_start_date');
            $table->date('subscription_end_date');

            $table->decimal('contract_amount', 15, 2);
            $table->decimal('commission_percentage', 5, 2)->default(0);

            $table->enum('status', ['active', 'expired', 'pending'])
                ->default('pending');

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_subscriptions');
    }
};
