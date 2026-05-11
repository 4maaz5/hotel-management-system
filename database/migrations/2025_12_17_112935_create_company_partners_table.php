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
        Schema::create('company_partners', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            $table->enum('partner_type', ['owner', 'investor']);

            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('nationality');

            $table->enum('id_type', ['national_id', 'iqama', 'passport']);
            $table->string('id_number');

            $table->decimal('investment_amount', 15, 2)->nullable();
            $table->decimal('share_percentage', 5, 2)->nullable();
            $table->integer('share_quantity')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_partners');
    }
};
