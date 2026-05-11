<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('website_pages')) {
            return;
        }

        Schema::table('website_pages', function (Blueprint $table): void {
            $table->dropUnique('website_pages_page_key_unique');
            $table->unique(['company_id', 'page_key'], 'website_pages_company_id_page_key_unique');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('website_pages')) {
            return;
        }

        Schema::table('website_pages', function (Blueprint $table): void {
            $table->dropUnique('website_pages_company_id_page_key_unique');
            $table->unique('page_key', 'website_pages_page_key_unique');
        });
    }
};
