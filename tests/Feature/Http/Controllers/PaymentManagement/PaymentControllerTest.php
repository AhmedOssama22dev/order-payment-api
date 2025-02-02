<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testItFetchesPaymentsWithFilters()
    {
        $user = User::factory()->create();
        Order::factory()->create(['user_id' => $user->id]);

        $token = JWTAuth::fromUser(User::factory()->create());
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson(route('payments.index', ['user_id' => $user->id]));

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Payments fetched successfully',
            ]);
    }

    public function testItHandlesEmptyPayments()
    {
        $token = JWTAuth::fromUser(User::factory()->create());
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->getJson(route('payments.index'));

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Payments fetched successfully',
                'data' => [],
            ]);
    }

    public function testItProcessesPaymentSuccessfully()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'confirmed']);

        $paymentData = [
            'order_id' => $order->id,
            'payment_method' => 'paypal',
        ];

        $token = JWTAuth::fromUser(User::factory()->create());
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->postJson(route('payments.process'), $paymentData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Payment processed successfully',
            ]);
    }

    public function testItFailsToProcessPaymentForNonConfirmedOrder()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'pending']);

        $paymentData = [
            'order_id' => $order->id,
            'payment_method' => 'paypal',
        ];

        $token = JWTAuth::fromUser(User::factory()->create());
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->postJson(route('payments.process'), $paymentData);

        $response->assertStatus(500)
            ->assertJson([
                'status' => 'error',
                'message' => 'Failed to process payment.',
            ]);
    }

    public function testItHandlesValidationErrorWhenProcessingPayment()
    {
        $invalidPaymentData = [
            'order_id' => null,
            'amount' => 0,
            'payment_method' => 'invalid_method',
        ];

        $token = JWTAuth::fromUser(User::factory()->create());
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->postJson(route('payments.process'), $invalidPaymentData);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'order_id' => ['The order ID is required.'],
                ],
            ]);
    }

    public function testItReturnsPaymentNotProcessedWhenPaymentFails()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $paymentData = [
            'order_id' => $order->id,
            'payment_method' => 'invalid_method', // Invalid payment method
        ];

        $token = JWTAuth::fromUser(User::factory()->create());
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->postJson(route('payments.process'), $paymentData);

        $response->assertStatus(500)
            ->assertJson([
                'status' => 'error',
                'message' => 'Failed to process payment.',
            ]);
    }
}
