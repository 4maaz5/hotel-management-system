<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        SubscriptionPlan::create([
            'name' => 'Basic',
            'description' => 'Perfect for small properties getting started.',
            'price' => 29.00,
            'billing_period' => 'monthly',
            'features' => [
                'booking_engine',
                'housekeeping',
                'invoicing',
            ],
            'limits' => [
                'max_users' => 3,
                'max_properties' => 1,
            ],
            'is_active' => true,
        ]);

        SubscriptionPlan::create([
            'name' => 'Pro',
            'description' => 'For growing businesses that need more power.',
            'price' => 79.00,
            'billing_period' => 'monthly',
            'features' => [
                'multi_property',
                'booking_engine',
                'housekeeping',
                'invoicing',
                'sms_notifications',
                'reports_analytics',
                'outlet_pos',
            ],
            'limits' => [
                'max_users' => 15,
                'max_properties' => 5,
            ],
            'is_active' => true,
        ]);

        SubscriptionPlan::create([
            'name' => 'Enterprise',
            'description' => 'Full feature set for large hotel chains.',
            'price' => 199.00,
            'billing_period' => 'monthly',
            'features' => [
                'multi_property',
                'hr_module',
                'booking_engine',
                'housekeeping',
                'invoicing',
                'sms_notifications',
                'reports_analytics',
                'channel_manager',
                'ai_chatbot',
                'outlet_pos',
                'api_access',
                'custom_branding',
            ],
            'limits' => [
                'max_users' => 0,
                'max_properties' => 0,
            ],
            'is_active' => true,
        ]);
    }
}
