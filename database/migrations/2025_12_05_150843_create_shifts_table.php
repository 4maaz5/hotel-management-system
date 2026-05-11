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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();

            // Branch relation
            $table->unsignedBigInteger('branch_id');

            $table->string('name');

            // Time settings
            $table->time('start_time');
            $table->time('end_time');

            $table->timestamps();

            // Foreign key
            $table->foreign('branch_id')
                ->references('id')->on('branches')
                ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
