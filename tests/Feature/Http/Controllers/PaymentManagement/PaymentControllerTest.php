<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Tests\TestCase;
use App\Services\PaymentManagement\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testFetchPaymentsWithFilters()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson(route('payments.index', ['user_id' => $user->id]));

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Payments fetched successfully',
            ]);
    }


    public function testHandlesEmptyPayments()
    {
        $response = $this->actingAs(User::factory()->create())->getJson(route('payments.index'));

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Payments fetched successfully',
                'data' => [],
            ]);
    }


    public function testProcessPaymentSuccessfully()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $order->status = 'confirmed';
        $order->save();

        $paymentData = [
            'order_id' => $order->id,
            'payment_method' => 'paypal', // TODO: make this more generic to be flexible with any gateway changes
        ];

        $response = $this->actingAs($user)->postJson(route('payments.process'), $paymentData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Payment processed successfully',
            ]);
    }

    public function testPayNonConfirmedOrder()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $order->status = 'pending';
        $order->save();

        $paymentData = [
            'order_id' => $order->id,
            'payment_method' => 'paypal', // TODO: make this more generic to be flexible with any gateway changes
        ];

        $response = $this->actingAs($user)->postJson(route('payments.process'), $paymentData);

        $response->assertStatus(500)
            ->assertJson([
                'status' => 'error',
                'message' => 'Failed to process payment.',
            ]);
    }

    public function testHandlesValidationErrorWhenProcessingPayment()
    {
        $invalidPaymentData = [
            'order_id' => null,
            'amount' => 0,
            'payment_method' => 'invalid_method',
        ];

        $response = $this->actingAs(User::factory()->create())->postJson(route('payments.process'), $invalidPaymentData);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'order_id' => ['The order ID is required.']
                ]
            ]);
    }


    public function testReturnsPaymentNotProcessedWhenPaymentFails()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $paymentData = [
            'order_id' => $order->id,
            'payment_method' => 'invalid_method', // Invalid payment method
        ];

        $response = $this->actingAs($user)->postJson(route('payments.process'), $paymentData);

        $response->assertStatus(500)
            ->assertJson([
                'status' => 'error',
                'message' => 'Failed to process payment.',
            ]);
    }
}
