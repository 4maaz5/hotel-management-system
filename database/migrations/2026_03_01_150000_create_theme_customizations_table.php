<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('theme_customizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->index();
            $table->string('sidebar_bg_color')->default('#1a237e');
            $table->string('sidebar_text_color')->default('#e8eaf6');
            $table->string('sidebar_active_color')->default('#3949ab');
            $table->string('sidebar_hover_color')->default('#303f9f');
            $table->string('topbar_bg_color')->default('#1a237e');
            $table->string('topbar_text_color')->default('#ffffff');
            $table->string('text_color')->default('#212529');
            $table->string('font_family')->default('Segoe UI, Tahoma, Geneva, Verdana, sans-serif');
            $table->string('background_image')->nullable();
            $table->string('logo')->nullable();
            $table->string('login_bg_color')->default('#1a237e');
            $table->string('login_text_color')->default('#ffffff');
            $table->string('login_card_bg')->default('#ffffff');
            $table->string('button_primary_color')->default('#1a237e');
            $table->string('button_secondary_color')->default('#6c757d');
            $table->string('card_bg_color')->default('#ffffff');
            $table->string('card_border_color')->default('#dee2e6');
            $table->string('table_header_bg')->default('#1a237e');
            $table->string('table_header_text')->default('#ffffff');
            $table->string('table_row_even')->default('#f8f9fa');
            $table->string('table_row_odd')->default('#ffffff');
            $table->string('input_bg_color')->default('#ffffff');
            $table->string('input_border_color')->default('#ced4da');
            $table->string('input_text_color')->default('#212529');
            $table->string('dashboard_card_bg')->default('#ffffff');
            $table->string('dashboard_card_border')->default('#dee2e6');
            $table->string('dashboard_icon_color')->default('#1a237e');
            $table->string('dashboard_card_title_color')->default('#212529');
            $table->string('dashboard_card_text_color')->default('#6c757d');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('theme_customizations');
    }
};

