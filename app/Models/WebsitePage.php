<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class WebsitePage extends Model
{
    use BelongsToTenant;

    public static function supportedPageKeys(): array
    {
        return [
            'faq',
            'contact',
        ];
    }

    protected $fillable = [
        'tenant_id',
        'page_key',
        'nav_label_en',
        'nav_label_ar',
        'title_en',
        'title_ar',
        'hero_title_en',
        'hero_title_ar',
        'hero_intro_en',
        'hero_intro_ar',
        'body_en',
        'body_ar',
        'seo_title_en',
        'seo_title_ar',
        'seo_description_en',
        'seo_description_ar',
        'seo_keywords_en',
        'seo_keywords_ar',
        'is_published',
        'show_in_navigation',
        'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'show_in_navigation' => 'boolean',
    ];

    public static function ensureDefaults(): void
    {
        foreach (static::defaultPages() as $pageKey => $attributes) {
            static::query()->firstOrCreate(
                ['page_key' => $pageKey],
                $attributes
            );
        }
    }

    public static function defaultPages(): array
    {
        $pages = [
            'about' => [
                'nav_label_en' => 'About',
                'nav_label_ar' => 'من نحن',
                'title_en' => 'About the hotel',
                'title_ar' => 'عن الفندق',
                'hero_title_en' => 'A professional hospitality presence that reflects your operation',
                'hero_title_ar' => 'حضور ضيافة احترافي يعكس تشغيلك الفعلي',
                'hero_intro_en' => 'Use this page to describe the property story, positioning, service style, and why guests should book direct.',
                'hero_intro_ar' => 'استخدم هذه الصفحة لشرح قصة العقار وتموضعه وأسلوب الخدمة ولماذا يجب على الضيف أن يحجز مباشرة.',
                'body_en' => "Introduce the property, service philosophy, and ideal guest segments.\n\nExplain your location strengths, room mix, and brand experience.",
                'body_ar' => "عرّف بالعقار وفلسفة الخدمة ونوع النزلاء المستهدفين.\n\nاشرح نقاط قوة الموقع وتنوع الغرف وتجربة العلامة.",
                'seo_title_en' => 'About our hotel',
                'seo_title_ar' => 'عن فندقنا',
                'seo_description_en' => 'Learn more about the property, guest experience, and reasons to book direct.',
                'seo_description_ar' => 'تعرّف أكثر على العقار وتجربة النزلاء وأسباب الحجز المباشر.',
                'show_in_navigation' => true,
                'sort_order' => 10,
            ],
            'offers' => [
                'nav_label_en' => 'Offers',
                'nav_label_ar' => 'العروض',
                'title_en' => 'Special offers',
                'title_ar' => 'العروض الخاصة',
                'hero_title_en' => 'Campaigns and stay offers designed for direct conversion',
                'hero_title_ar' => 'حملات وعروض إقامة مصممة لرفع التحويل المباشر',
                'hero_intro_en' => 'Use this page for promo campaigns, seasonal pushes, and package messaging.',
                'hero_intro_ar' => 'استخدم هذه الصفحة للحملات الترويجية والعروض الموسمية ورسائل الباقات.',
                'body_en' => "Publish direct-booking campaigns such as early booking, last minute, or long-stay offers.\n\nKeep the message concise, trustworthy, and time-sensitive.",
                'body_ar' => "انشر حملات الحجز المباشر مثل الحجز المبكر أو اللحظات الأخيرة أو عروض الإقامة الطويلة.\n\nاجعل الرسالة مختصرة وموثوقة وتحمل عنصر الوقت.",
                'seo_title_en' => 'Hotel offers',
                'seo_title_ar' => 'عروض الفندق',
                'seo_description_en' => 'Explore hotel offers and direct-booking promotions.',
                'seo_description_ar' => 'استكشف عروض الفندق وحملات الحجز المباشر.',
                'show_in_navigation' => true,
                'sort_order' => 20,
            ],
            'facilities' => [
                'nav_label_en' => 'Facilities',
                'nav_label_ar' => 'المرافق',
                'title_en' => 'Facilities and services',
                'title_ar' => 'المرافق والخدمات',
                'hero_title_en' => 'Facilities that support the booking decision',
                'hero_title_ar' => 'مرافق تدعم قرار الحجز',
                'hero_intro_en' => 'This page combines CMS copy with the live facilities already managed in your setup dashboard.',
                'hero_intro_ar' => 'تجمع هذه الصفحة بين محتوى الموقع والمرافق الفعلية المُدارة داخل لوحة الإعدادات.',
                'body_en' => "Describe the atmosphere, services, and guest value before showing the facility list.\n\nThis helps the page rank better and feel more complete.",
                'body_ar' => "اشرح الأجواء والخدمات والقيمة المقدمة للنزيل قبل عرض قائمة المرافق.\n\nهذا يجعل الصفحة أكثر اكتمالاً وأفضل لمحركات البحث.",
                'seo_title_en' => 'Hotel facilities',
                'seo_title_ar' => 'مرافق الفندق',
                'seo_description_en' => 'Review hotel facilities, amenities, and guest services.',
                'seo_description_ar' => 'اطّلع على مرافق الفندق ووسائل الراحة والخدمات المقدمة للنزلاء.',
                'show_in_navigation' => true,
                'sort_order' => 30,
            ],
            'policies' => [
                'nav_label_en' => 'Policies',
                'nav_label_ar' => 'السياسات',
                'title_en' => 'Stay policies',
                'title_ar' => 'سياسات الإقامة',
                'hero_title_en' => 'Policies published directly from your operations',
                'hero_title_ar' => 'سياسات منشورة مباشرة من تشغليك الفعلي',
                'hero_intro_en' => 'Use this page to introduce the terms, then show the active hotel policies maintained by your team.',
                'hero_intro_ar' => 'استخدم هذه الصفحة لتقديم البنود ثم عرض سياسات الفندق النشطة التي يديرها فريقك.',
                'body_en' => "Make your cancellation, check-in, and payment terms easier to understand before guests reach checkout.",
                'body_ar' => "اجعل شروط الإلغاء وتسجيل الدخول والدفع أوضح قبل أن يصل الضيف إلى الدفع.",
                'seo_title_en' => 'Hotel policies',
                'seo_title_ar' => 'سياسات الفندق',
                'seo_description_en' => 'Review hotel stay policies, check-in terms, and important reservation rules.',
                'seo_description_ar' => 'راجع سياسات الإقامة وشروط الوصول وأهم قواعد الحجز.',
                'show_in_navigation' => true,
                'sort_order' => 40,
            ],
            'faq' => [
                'nav_label_en' => 'FAQ',
                'nav_label_ar' => 'الأسئلة الشائعة',
                'title_en' => 'Frequently asked questions',
                'title_ar' => 'الأسئلة الشائعة',
                'hero_title_en' => 'Answers that remove booking hesitation',
                'hero_title_ar' => 'إجابات تقلل تردد الحجز',
                'hero_intro_en' => 'Manage the visible questions from the dashboard and keep answers aligned with operations.',
                'hero_intro_ar' => 'أدر الأسئلة الظاهرة من لوحة التحكم واجعل الإجابات متوافقة مع التشغيل الفعلي.',
                'body_en' => "Use FAQs to answer practical guest concerns about payments, children, arrival times, and support.",
                'body_ar' => "استخدم صفحة الأسئلة الشائعة للإجابة عن استفسارات الضيوف حول الدفع والأطفال وأوقات الوصول والدعم.",
                'seo_title_en' => 'Hotel FAQ',
                'seo_title_ar' => 'الأسئلة الشائعة للفندق',
                'seo_description_en' => 'Read the most common hotel booking questions and answers.',
                'seo_description_ar' => 'اقرأ أكثر أسئلة الحجز الفندقي شيوعاً وإجاباتها.',
                'show_in_navigation' => true,
                'sort_order' => 50,
            ],
            'contact' => [
                'nav_label_en' => 'Contact',
                'nav_label_ar' => 'اتصل بنا',
                'title_en' => 'Contact us',
                'title_ar' => 'اتصل بنا',
                'hero_title_en' => 'Clear contact and support information',
                'hero_title_ar' => 'معلومات تواصل ودعم واضحة',
                'hero_intro_en' => 'Use this page to publish the best support message while still pulling live property contact details.',
                'hero_intro_ar' => 'استخدم هذه الصفحة لنشر أفضل رسالة دعم مع الاستفادة من بيانات التواصل الفعلية للعقار.',
                'body_en' => "Add your response promise, support channels, and directions message here.",
                'body_ar' => "أضف هنا وعد الاستجابة وقنوات الدعم ورسالة الوصول إلى الموقع.",
                'seo_title_en' => 'Contact the hotel',
                'seo_title_ar' => 'التواصل مع الفندق',
                'seo_description_en' => 'Find hotel contact details, direct support, and location information.',
                'seo_description_ar' => 'اعثر على بيانات التواصل والدعم المباشر ومعلومات الموقع.',
                'show_in_navigation' => true,
                'sort_order' => 60,
            ],
        ];

        return array_intersect_key($pages, array_flip(static::supportedPageKeys()));
    }
}
