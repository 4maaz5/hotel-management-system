<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        SubscriptionPlan::updateOrCreate(['name' => 'Trial Free'], [
            'description' => 'Free trial plan for evaluating the platform.',
            'price' => 0.00,
            'billing_period' => 'monthly',
            'features' => [],
            'limits' => [
                'max_users' => 50,
                'max_properties' => 1,
            ],
            'is_active' => true,
        ]);

        SubscriptionPlan::updateOrCreate(['name' => 'Basic'], [
            'name' => 'Basic',
            'description' => 'Perfect for small properties getting started.',
            'price' => 29.00,
            'billing_period' => 'monthly',
            'features' => [],
            'limits' => [
                'max_users' => 200,
                'max_properties' => 1,
            ],
            'is_active' => true,
        ]);

        SubscriptionPlan::updateOrCreate(['name' => 'Pro'], [
            'name' => 'Pro',
            'description' => 'For growing businesses that need more power.',
            'price' => 79.00,
            'billing_period' => 'monthly',
            'features' => [],
            'limits' => [
                'max_users' => 400,
                'max_properties' => 5,
            ],
            'is_active' => true,
        ]);

        SubscriptionPlan::updateOrCreate(['name' => 'Enterprise'], [
            'name' => 'Enterprise',
            'description' => 'Full feature set for large hotel chains.',
            'price' => 199.00,
            'billing_period' => 'monthly',
            'features' => ['custom_branding'],
            'limits' => [
                'max_users' => 1000,
                'max_properties' => 0,
            ],
            'is_active' => true,
        ]);
    }
}
