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
        Schema::create('promissory_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->index();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('voucher_number');
            $table->enum('voucher_type', ['manual', 'reservation'])->default('manual');
            $table->date('date');
            $table->time('time');
            $table->date('maturity_date');
            $table->string('reserved_to');
            $table->string('purpose');
            $table->string('maturity_place');
            $table->decimal('amount', 12, 2);
            $table->decimal('collected_amount', 12, 2)->default(0);
            $table->text('comment')->nullable();
            $table->foreignId('reservation_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('guest_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('payment_method_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('receiving_bank_id')->nullable()->constrained('banks')->onDelete('set null');
            $table->string('transaction_number')->nullable();
            $table->string('sending_bank_name')->nullable();
            $table->string('cheque_number')->nullable();
            $table->enum('status', ['pending', 'partial', 'collected', 'cancelled'])->default('pending');
            $table->text('cancel_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promissory_notes');
    }
};

