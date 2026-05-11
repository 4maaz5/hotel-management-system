<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ntmp_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_type', 30);
            $table->string('status', 30)->default('pending');
            $table->string('payload_hash', 64)->nullable();
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->string('external_reference')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('attempted_at')->nullable();
            $table->timestamps();

            $table->index(['branch_id', 'status']);
            $table->index(['reservation_id', 'event_type']);
            $table->unique(['branch_id', 'reservation_id', 'event_type', 'payload_hash'], 'ntmp_submission_payload_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ntmp_submissions');
    }
};
