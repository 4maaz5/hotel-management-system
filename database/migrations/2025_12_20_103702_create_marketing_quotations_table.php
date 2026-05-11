<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_quotations', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('marketing_agent_id')
                ->constrained('marketing_agents')
                ->cascadeOnDelete();

            $table->foreignId('branch_id')
                ->constrained('branches')
                ->cascadeOnDelete();

            // Quotation Info
            $table->string('quotation_number')->unique();
            $table->string('client_name');
            $table->string('client_contact')->nullable();
            $table->text('description')->nullable();
            $table->decimal('quotation_amount', 15, 2);

            // Approval & Status
            $table->dateTime('approved_at')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->timestamps();

            // Indexes for performance
            $table->index(['marketing_agent_id', 'branch_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_quotations');
    }
};
