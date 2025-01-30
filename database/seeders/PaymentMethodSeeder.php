<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [
            ['name' => 'PayPal', 'description' => 'Pay securely via PayPal.'],
            ['name' => 'Credit Card', 'description' => 'Visa, MasterCard, and other credit cards.'],
            ['name' => 'Stripe', 'description' => 'Fast and secure payments via Stripe.'],
            ['name' => 'Paymob', 'description' => 'Pay using Paymob gateway.'],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::updateOrCreate(['name' => $method['name']], $method);
        }
    }
}
