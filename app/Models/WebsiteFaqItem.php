<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class WebsiteFaqItem extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'question_en',
        'question_ar',
        'answer_en',
        'answer_ar',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public static function ensureDefaults(): void
    {
        if (static::query()->exists()) {
            return;
        }

        foreach ([
            [
                'question_en' => 'Can I book instantly through the website?',
                'question_ar' => 'هل يمكنني الحجز مباشرة من خلال الموقع؟',
                'answer_en' => 'Yes. Direct bookings are created inside the reservation system and availability is checked before the request is saved.',
                'answer_ar' => 'نعم. يتم إنشاء الحجوزات المباشرة داخل نظام إدارة الحجوزات مع التحقق من التوفر قبل حفظ الطلب.',
                'sort_order' => 10,
            ],
            [
                'question_en' => 'Are prices on the website connected to live rates?',
                'question_ar' => 'هل أسعار الموقع مرتبطة بالأسعار الفعلية؟',
                'answer_en' => 'Yes. The website uses the current rate setup, taxes, and booking rules already managed by your team.',
                'answer_ar' => 'نعم. يعتمد الموقع على إعدادات الأسعار والضرائب وقواعد الحجز الحالية التي يديرها فريقك.',
                'sort_order' => 20,
            ],
            [
                'question_en' => 'Will I receive a reservation reference after booking?',
                'question_ar' => 'هل سأستلم مرجعاً للحجز بعد الإرسال؟',
                'answer_en' => 'A booking reference is generated as soon as the reservation is saved so the team and guest can follow up easily.',
                'answer_ar' => 'يتم إنشاء مرجع للحجز فور حفظ الطلب حتى يتمكن الفريق والضيف من المتابعة بسهولة.',
                'sort_order' => 30,
            ],
        ] as $item) {
            static::query()->create($item);
        }
    }
}
