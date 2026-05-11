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
        Schema::table('users', function (Blueprint $table) {
            $table->json('profile_data')->nullable()->after('password');
            $table->json('employment_data')->nullable()->after('profile_data');
            $table->json('contact_info')->nullable()->after('employment_data');
            $table->string('status')->default('active')->after('contact_info');
            $table->string('default_language', 10)->default('en')->after('status');
            $table->string('user_type')->nullable()->after('default_language');
            $table->date('expiry_date')->nullable()->after('user_type');
            $table->foreignId('outlet_id')
                ->nullable()->after('branch_id')->constrained('outlets')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['profile_data', 'employment_data', 'contact_info', 'status', 'default_language', 'user_type']);
        });
    }
};
