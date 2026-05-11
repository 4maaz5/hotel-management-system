<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class WebsiteSetting extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'company_id',
        'site_tagline_en',
        'site_tagline_ar',
        'home_hero_kicker_en',
        'home_hero_kicker_ar',
        'home_hero_title_en',
        'home_hero_title_ar',
        'home_hero_text_en',
        'home_hero_text_ar',
        'featured_rooms_title_en',
        'featured_rooms_title_ar',
        'featured_rooms_intro_en',
        'featured_rooms_intro_ar',
        'facilities_section_title_en',
        'facilities_section_title_ar',
        'facilities_section_intro_en',
        'facilities_section_intro_ar',
        'rooms_page_title_en',
        'rooms_page_title_ar',
        'rooms_page_intro_en',
        'rooms_page_intro_ar',
        'footer_note_en',
        'footer_note_ar',
        'contact_phone_override',
        'contact_email_override',
        'default_seo_title_en',
        'default_seo_title_ar',
        'default_seo_description_en',
        'default_seo_description_ar',
    ];

    public static function getSettings(): self
    {
        return static::query()->first() ?? static::createDefault();
    }

    public static function createDefault(): self
    {
        return static::create([
            'site_tagline_en' => 'Direct hotel booking with live availability and a cleaner guest journey.',
            'site_tagline_ar' => 'حجز فندقي مباشر بتوفر حي وتجربة نزيل أوضح.',
            'home_hero_kicker_en' => 'Direct Booking',
            'home_hero_kicker_ar' => 'الحجز المباشر',
            'home_hero_title_en' => 'Professional booking pages connected to your live reservation system',
            'home_hero_title_ar' => 'صفحات حجز احترافية مرتبطة مباشرة بنظام إدارة الحجوزات',
            'home_hero_text_en' => 'The public site sells room types clearly while your PMS continues to control units, pricing, taxes, and reservation status.',
            'home_hero_text_ar' => 'يعرض الموقع العام أنواع الغرف بشكل أوضح، بينما يستمر نظام الإدارة في التحكم بالوحدات والأسعار والضرائب وحالة الحجز.',
            'featured_rooms_title_en' => 'Featured room types',
            'featured_rooms_title_ar' => 'أنواع الغرف المميزة',
            'featured_rooms_intro_en' => 'Highlight the best room products for direct booking and faster comparison.',
            'featured_rooms_intro_ar' => 'اعرض أفضل منتجات الغرف للحجز المباشر والمقارنة السريعة.',
            'facilities_section_title_en' => 'Facilities guests care about',
            'facilities_section_title_ar' => 'مرافق يهتم بها النزلاء',
            'facilities_section_intro_en' => 'Use your existing property facilities to strengthen trust on the website.',
            'facilities_section_intro_ar' => 'استخدم مرافق العقار الحالية لتعزيز الثقة داخل الموقع.',
            'rooms_page_title_en' => 'Rooms and suites',
            'rooms_page_title_ar' => 'الغرف والأجنحة',
            'rooms_page_intro_en' => 'Compare live availability, occupancy, and room benefits in a mobile-friendly layout.',
            'rooms_page_intro_ar' => 'قارن التوفر المباشر والسعة ومزايا الغرف ضمن تصميم مناسب للجوال.',
            'footer_note_en' => 'This direct website is powered by your reservation dashboard and stays aligned with live inventory.',
            'footer_note_ar' => 'هذا الموقع المباشر يعمل من خلال لوحة إدارة الحجوزات ويبقى متوافقاً مع التوفر الفعلي.',
            'default_seo_title_en' => 'Direct hotel booking',
            'default_seo_title_ar' => 'الحجز الفندقي المباشر',
            'default_seo_description_en' => 'Book direct through a professional hotel website connected to live rooms, rates, and policies.',
            'default_seo_description_ar' => 'احجز مباشرة من خلال موقع فندقي احترافي مرتبط بالغرف والأسعار والسياسات الفعلية.',
        ]);
    }
}
