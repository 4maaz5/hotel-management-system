<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->foreignId('letter_setting_id')
                ->nullable()
                ->constrained('letter_settings')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->dropForeign(['letter_setting_id']);
            $table->dropColumn('letter_setting_id');
        });
    }
};
