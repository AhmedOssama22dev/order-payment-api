<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled']),
            'user_id' => User::factory(),
            'total_price' => $this->faker->randomFloat(2, 100, 1000),
        ];
    }
}
