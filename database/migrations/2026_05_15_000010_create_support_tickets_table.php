<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subject');
            $table->string('category')->nullable();
            $table->string('support_area', 30)->default('reservation');
            $table->string('priority', 20)->default('normal');
            $table->string('status', 20)->default('open');
            $table->timestamp('last_message_at')->nullable();
            $table->string('last_sender_role', 30)->nullable();
            $table->timestamp('tenant_last_read_at')->nullable();
            $table->timestamp('super_admin_last_read_at')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'support_area', 'status']);
            $table->index(['status', 'last_message_at']);
            $table->index(['last_sender_role', 'last_message_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
