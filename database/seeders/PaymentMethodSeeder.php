<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            'Digital Payments: Paytabs',
            'Credit Cards: GCCNET',
            'Booking.com',
            'Agoda.com',
            'AirBnb.com',
            'Gathern',
            'Expedia',
            'Keeta Cash',
            'OYO',
            'Digital Payments',
            'Cash',
            'Mada',
            'Bank Transfer',
            'Cheque',
            'Online Payment',
        ];

        foreach ($methods as $name) {
            PaymentMethod::updateOrCreate(
                ['name' => $name]
            );
        }
    }
}
