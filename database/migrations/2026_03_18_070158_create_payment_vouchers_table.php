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
        Schema::create('payment_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->index();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('voucher_number');
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->unsignedBigInteger('cost_center_id')->nullable();
            $table->string('purpose')->nullable();
            $table->enum('voucher_type', ['payment', 'refund'])->default('payment');
            $table->text('comment')->nullable();
            $table->string('vendor_name')->nullable();
            $table->string('vendor_tax_no')->nullable();
            $table->string('vendor_invoice_no')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('vat_amount', 15, 2)->default(0);
            $table->decimal('amount_before_vat', 15, 2)->default(0);
            $table->boolean('apply_vat')->default(false);
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->unsignedBigInteger('receiving_bank_id')->nullable();
            $table->string('transaction_number')->nullable();
            $table->string('sending_bank_name')->nullable();
            $table->string('cheque_number')->nullable();
            $table->unsignedBigInteger('reservation_id')->nullable();
            $table->unsignedBigInteger('guest_id')->nullable();
            $table->string('status')->default('active');
            $table->text('cancel_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_vouchers');
    }
};

