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
        Schema::create('guest_classes', function (Blueprint $table) {

            $table->id();
            $table->foreignId('company_id')->nullable()->index();

            $table->boolean('blacklist')->default(false);

            $table->json('class_name')->nullable();

            $table->integer('order_no')->default(1);

            $table->string('icon')->nullable();

            $table->string('discount_method')->nullable();

            $table->decimal('discount_amount', 10, 2)->nullable();

            $table->json('description')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_classes');
    }
};
