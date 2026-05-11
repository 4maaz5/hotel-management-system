<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('night_audit_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->index();
            $table->boolean('is_active')->default(false);
            $table->integer('allowance_period')->default(0);
            $table->integer('cancellation_threshold')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('night_audit_settings');
    }
};

