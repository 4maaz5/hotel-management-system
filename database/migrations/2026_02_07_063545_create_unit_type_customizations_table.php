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
        Schema::create('unit_type_customizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->index();

            $table->foreignId('unit_type_id')
                ->constrained('unit_types')
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('website_name_en')->nullable();
            $table->string('website_name_ar')->nullable();
            $table->decimal('unit_area', 8, 2)->nullable();
            $table->unsignedInteger('single_beds')->default(0);
            $table->unsignedInteger('double_beds')->default(0);
            $table->unsignedInteger('base_occupancy')->default(1);
            $table->text('description')->nullable();
            $table->string('website_summary_en', 500)->nullable();
            $table->string('website_summary_ar', 500)->nullable();
            $table->text('website_description_en')->nullable();
            $table->text('website_description_ar')->nullable();
            $table->string('website_slug')->nullable()->unique();
            $table->string('seo_title_en')->nullable();
            $table->string('seo_title_ar')->nullable();
            $table->string('seo_description_en', 500)->nullable();
            $table->string('seo_description_ar', 500)->nullable();
            $table->boolean('is_published_online')->default(true);
            $table->unsignedInteger('website_sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_type_customizations');
    }
};
