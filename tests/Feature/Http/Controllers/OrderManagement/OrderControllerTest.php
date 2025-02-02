<?php

namespace Tests\Feature\Http\Controllers\OrderManagement;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testItFetchesOrdersWithFilters()
    {
        $user = User::factory()->create();
        $orders = Order::factory()->count(5)->create();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson(route('orders.index', ['user_id' => $user->id]));

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Orders fetched successfully',
                'data' => $orders->toArray(),
            ]);
    }


    public function testItHandlesEmptyListWhenFetchingOrders()
    {
        $user = User::factory()->create();

        // Generate JWT token
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson(route('orders.index'));

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Orders fetched successfully',
                'data' => [],
            ]);
    }

    public function testItCreatesOrderSuccessfully()
    {
        $user = User::factory()->create();
        $productData = [
            'user_id' => $user->id,
            'products' => [
                ['name' => 1, 'quantity' => 2, 'price' => 100],
                ['name' => 2, 'quantity' => 1, 'price' => 50],
            ]
        ];

        // Generate JWT token
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson(route('orders.store'), $productData);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Order created successfully!',
            ]);
    }

    public function testItValidatesOrderCreationData()
    {
        $invalidData = [
            'products' => [
                ['product_id' => 1, 'quantity' => 2],
            ]
        ];

        // Generate JWT token
        $token = JWTAuth::fromUser(User::factory()->create());

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson(route('orders.store'), $invalidData);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'Validation failed.',
            ]);
    }

    public function testItFetchesOrderById()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create();

        // Generate JWT token
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson(route('orders.show', $order->id));

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Order fetched successfully!',
            ]);
    }

    public function testItReturnsErrorWhenOrderNotFound()
    {
        $token = JWTAuth::fromUser(User::factory()->create());

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson(route('orders.show', 'non-existent-id'));

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Order not found.',
            ]);
    }

    public function testItUpdatesOrderSuccessfully()
    {
        $order = Order::factory()->create();
        $updatedData = [
            'products' => [
                ['product_id' => 1, 'quantity' => 5],
            ]
        ];

        // Generate JWT token
        $token = JWTAuth::fromUser($order->user);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->putJson(route('orders.update', $order->id), $updatedData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Order updated successfully!',
            ]);
    }

    public function testItHandlesErrorWhenUpdatingOrder()
    {
        $token = JWTAuth::fromUser(User::factory()->create());

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->putJson(route('orders.update', 'non-existent-id'), []);

        $response->assertStatus(500)
            ->assertJson([
                'status' => 'error',
                'message' => 'Failed to update order.',
            ]);
    }

    public function testItDeletesOrderSuccessfully()
    {
        $order = Order::factory()->create();

        // Generate JWT token
        $token = JWTAuth::fromUser($order->user);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->deleteJson(route('orders.destroy', $order->id));

        $response->assertStatus(204);
    }

    public function testItHandlesErrorWhenDeletingOrder()
    {
        $token = JWTAuth::fromUser(User::factory()->create());

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->deleteJson(route('orders.destroy', 'non-existent-id'));

        $response->assertStatus(500)
            ->assertJson([
                'status' => 'error',
                'message' => 'Failed to delete order.',
            ]);
    }
}
