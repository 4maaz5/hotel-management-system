<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unit_type_customizations', function (Blueprint $table) {
            $table->string('website_name_en')->nullable()->after('name');
            $table->string('website_name_ar')->nullable()->after('website_name_en');
            $table->string('website_summary_en', 500)->nullable()->after('description');
            $table->string('website_summary_ar', 500)->nullable()->after('website_summary_en');
            $table->text('website_description_en')->nullable()->after('website_summary_ar');
            $table->text('website_description_ar')->nullable()->after('website_description_en');
            $table->string('website_slug')->nullable()->unique()->after('website_description_ar');
            $table->string('seo_title_en')->nullable()->after('website_slug');
            $table->string('seo_title_ar')->nullable()->after('seo_title_en');
            $table->string('seo_description_en', 500)->nullable()->after('seo_title_ar');
            $table->string('seo_description_ar', 500)->nullable()->after('seo_description_en');
            $table->boolean('is_published_online')->default(true)->after('seo_description_ar');
            $table->unsignedInteger('website_sort_order')->default(0)->after('is_published_online');
        });
    }

    public function down(): void
    {
        Schema::table('unit_type_customizations', function (Blueprint $table) {
            $table->dropUnique(['website_slug']);
            $table->dropColumn([
                'website_name_en',
                'website_name_ar',
                'website_summary_en',
                'website_summary_ar',
                'website_description_en',
                'website_description_ar',
                'website_slug',
                'seo_title_en',
                'seo_title_ar',
                'seo_description_en',
                'seo_description_ar',
                'is_published_online',
                'website_sort_order',
            ]);
        });
    }
};
