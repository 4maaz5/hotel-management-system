<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::statement("ALTER TABLE notifications MODIFY recipient_type
        ENUM('super_admin','manager','employee','department')
        NOT NULL
    ");
    }

    public function down()
    {
        DB::statement("ALTER TABLE notifications MODIFY recipient_type
        ENUM('super_admin','manager','employee')
        NOT NULL
    ");
    }
};
