<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cancel_reason_penalty', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cancel_reason_id');
            $table->unsignedBigInteger('penalty_id');
            $table->boolean('auto_apply')->default(false);
            $table->timestamps();
            $table->foreign('cancel_reason_id')->references('id')->on('cancel_reasons')->onDelete('cascade');
            $table->foreign('penalty_id')->references('id')->on('penalties')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cancel_reason_penalty');
    }
};
