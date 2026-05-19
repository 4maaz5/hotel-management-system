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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('name');
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->json('profile_data')->nullable();
            $table->json('employment_data')->nullable();
            $table->json('contact_info')->nullable();
            $table->string('role')->default('employee');
            $table->unsignedBigInteger('branch_id')->nullable()->index();
            $table->unsignedBigInteger('outlet_id')->nullable()->index();
            $table->string('status')->default('active');
            $table->string('default_language', 10)->default('en');
            $table->string('user_type')->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('active_status')->default(0);
            $table->string('avatar')->default(config('chatify.user_avatar.default'));
            $table->boolean('dark_mode')->default(0);
            $table->string('messenger_color')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->unique(['company_id', 'email'], 'users_company_email_unique');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
