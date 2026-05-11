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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('branch_id'); // Foreign key to branches
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            // $table->unsignedBigInteger('company_id'); // Foreign key to branches
            // $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            // $table->unsignedBigInteger('brand_id'); // Foreign key to branches
            // $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
