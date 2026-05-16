<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->string('last_sender_role', 30)->nullable()->after('last_message_at');
            $table->timestamp('tenant_last_read_at')->nullable()->after('last_sender_role');
            $table->timestamp('super_admin_last_read_at')->nullable()->after('tenant_last_read_at');
            $table->index(['last_sender_role', 'last_message_at']);
        });
    }

    public function down(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropIndex(['last_sender_role', 'last_message_at']);
            $table->dropColumn([
                'last_sender_role',
                'tenant_last_read_at',
                'super_admin_last_read_at',
            ]);
        });
    }
};
