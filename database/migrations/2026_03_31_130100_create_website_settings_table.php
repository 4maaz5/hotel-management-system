<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->index();
            $table->string('site_tagline_en')->nullable();
            $table->string('site_tagline_ar')->nullable();
            $table->string('home_hero_kicker_en')->nullable();
            $table->string('home_hero_kicker_ar')->nullable();
            $table->string('home_hero_title_en')->nullable();
            $table->string('home_hero_title_ar')->nullable();
            $table->text('home_hero_text_en')->nullable();
            $table->text('home_hero_text_ar')->nullable();
            $table->string('featured_rooms_title_en')->nullable();
            $table->string('featured_rooms_title_ar')->nullable();
            $table->text('featured_rooms_intro_en')->nullable();
            $table->text('featured_rooms_intro_ar')->nullable();
            $table->string('facilities_section_title_en')->nullable();
            $table->string('facilities_section_title_ar')->nullable();
            $table->text('facilities_section_intro_en')->nullable();
            $table->text('facilities_section_intro_ar')->nullable();
            $table->string('policies_section_title_en')->nullable();
            $table->string('policies_section_title_ar')->nullable();
            $table->text('policies_section_intro_en')->nullable();
            $table->text('policies_section_intro_ar')->nullable();
            $table->string('rooms_page_title_en')->nullable();
            $table->string('rooms_page_title_ar')->nullable();
            $table->text('rooms_page_intro_en')->nullable();
            $table->text('rooms_page_intro_ar')->nullable();
            $table->text('footer_note_en')->nullable();
            $table->text('footer_note_ar')->nullable();
            $table->string('contact_phone_override')->nullable();
            $table->string('contact_email_override')->nullable();
            $table->string('default_seo_title_en')->nullable();
            $table->string('default_seo_title_ar')->nullable();
            $table->string('default_seo_description_en', 500)->nullable();
            $table->string('default_seo_description_ar', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_settings');
    }
};

