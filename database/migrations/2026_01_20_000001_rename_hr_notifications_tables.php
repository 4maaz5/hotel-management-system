<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('notifications', 'system_notifications');
        Schema::rename('notification_reads', 'system_notification_reads');
    }

    public function down(): void
    {
        Schema::rename('system_notifications', 'notifications');
        Schema::rename('system_notification_reads', 'notification_reads');
    }
};
