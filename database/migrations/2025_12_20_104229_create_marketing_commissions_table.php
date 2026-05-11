<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_commissions', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('marketing_agent_id')
                ->constrained('marketing_agents')
                ->cascadeOnDelete();

            $table->foreignId('branch_id')
                ->constrained('branches')
                ->cascadeOnDelete();

            $table->foreignId('quotation_id')
                ->constrained('marketing_quotations')
                ->cascadeOnDelete();

            // Commission Info
            $table->decimal('commission_percentage', 5, 2);
            $table->decimal('commission_amount', 15, 2);

            $table->enum('paid_status', ['pending', 'paid'])->default('pending');

            $table->timestamps();

            // Indexes
            $table->unique('quotation_id'); // one commission per quotation
            $table->index(['marketing_agent_id', 'branch_id', 'paid_status'], 'mc_agent_branch_paid_idx');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_commissions');
    }
};
