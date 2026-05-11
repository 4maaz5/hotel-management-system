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
        Schema::create('tax_fee_customizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->index();

            $table->boolean('is_expenses')->default(false);

            $table->enum('type', ['tax', 'fee']);

            $table->string('custom_name');

            $table->enum('method', [
                'percentage',
                'fixed_amount_reservation',
                'fixed_amount_per_night',
            ]);

            $table->decimal('amount', 12, 2);

            $table->json('applied_on');

            $table->boolean('has_max_length')->default(false);
            $table->integer('max_length')->nullable();

            $table->date('start_date');
            $table->date('end_date')->nullable();

            $table->boolean('charged_on_fees')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_fee_customizations');
    }
};

