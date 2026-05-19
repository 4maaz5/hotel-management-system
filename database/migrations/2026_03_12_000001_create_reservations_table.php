<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->index();
            $table->string('reservation_number');
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('corporate_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('penalty_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cancel_reason_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('source_id')->nullable()->constrained('reservation_source_settings')->nullOnDelete();
            $table->foreignId('guest_class_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('rate_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('payment_method_id')->nullable()->constrained()->nullOnDelete();

            $table->date('check_in_date');
            $table->time('check_in_time')->nullable();
            $table->date('check_out_date');
            $table->time('check_out_time')->nullable();
            $table->integer('nights');
            $table->integer('adults')->default(1);
            $table->integer('children')->default(0);

            $table->enum('reservation_type', ['daily', 'monthly'])->default('daily');
            $table->decimal('penalty_amount', 12, 2)->default(0);
            $table->decimal('daily_rate', 12, 2)->default(0);
            $table->decimal('monthly_rate', 12, 2)->default(0);
            $table->decimal('total_rent', 12, 2)->default(0);
            $table->string('discount_type')->nullable();
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total_taxes_fees', 12, 2)->default(0);
            $table->decimal('security_deposit', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('balance', 12, 2)->default(0);

            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);

            $table->enum('status', ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'no_show'])->default('pending');
            $table->boolean('is_confirmed')->default(false);
            $table->date('booking_date')->nullable();
            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->timestamp('no_show_at')->nullable();
            $table->timestamp('shomoos_reported_at')->nullable();
            $table->timestamp('ntmp_reported_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'reservation_number'], 'reservations_company_number_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
