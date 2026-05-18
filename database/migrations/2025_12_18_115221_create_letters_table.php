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
        Schema::create('letters', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('employee_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->string('receiver_name')->nullable();
            $table->unsignedBigInteger('letter_setting_id')->nullable()->index();

            $table->enum('letter_type', ['open', 'warning']);

            $table->string('letter_number', 50);

            $table->string('subject', 255);

            $table->longText('body');

            $table->date('hijri_date');

            $table->dateTime('gregorian_date');

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('pdf_path')->nullable();

            $table->timestamps();

            $table->unique(['company_id', 'letter_number'], 'letters_company_letter_number_unique');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letters');
    }
};
