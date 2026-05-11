<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_chat_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->index();
            $table->foreignId('session_id')->nullable()->constrained('chat_sessions')->nullOnDelete();
            $table->foreignId('message_id')->nullable()->constrained('chat_messages')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('intent')->nullable();
            $table->string('language', 5)->default('en');
            $table->string('tool_name')->nullable();
            $table->string('status')->default('queued');
            $table->json('request_payload')->nullable();
            $table->json('plan_payload')->nullable();
            $table->json('tool_payload')->nullable();
            $table->json('tool_result')->nullable();
            $table->json('response_payload')->nullable();
            $table->longText('error_message')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('intent');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_chat_logs');
    }
};

