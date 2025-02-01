<?php

namespace Tests\Feature\Http\Controllers\OrderManagement;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Order;
use App\Models\User;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testItFetchesOrdersWithFilters()
    {
        $user = User::factory()->create();
        $orders = Order::factory()->count(5)->create();

        $response = $this->actingAs($user)->getJson(route('orders.index', ['user_id' => $user->id]));

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Orders fetched successfully',
                'data' => $orders->toArray(),
            ]);
    }


    public function testItHandlesEmptyListWhenFetchingOrders()
    {
        $response = $this->actingAs(User::factory()->create())->getJson(route('orders.index'));

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

        $response = $this->actingAs($user)->postJson(route('orders.store'), $productData);

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

        $response = $this->actingAs(User::factory()->create())->postJson(route('orders.store'), $invalidData);

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

        $response = $this->actingAs($user)->getJson(route('orders.show', $order->id));

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Order fetched successfully!',
            ]);
    }


    public function testItReturnsErrorWhenOrderNotFound()
    {
        $response = $this->actingAs(User::factory()->create())->getJson(route('orders.show', 'non-existent-id'));

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

        $response = $this->actingAs($order->user)->putJson(route('orders.update', $order->id), $updatedData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Order updated successfully!',
            ]);
    }


    public function testItHandlesErrorWhenUpdatingOrder()
    {
        $response = $this->actingAs(User::factory()->create())->putJson(route('orders.update', 'non-existent-id'), []);

        $response->assertStatus(500)
            ->assertJson([
                'status' => 'error',
                'message' => 'Failed to update order.',
            ]);
    }


    public function testItDeletesOrderSuccessfully()
    {
        $order = Order::factory()->create();

        $response = $this->actingAs($order->user)->deleteJson(route('orders.destroy', $order->id));

        $response->assertStatus(204);
    }


    public function testItHandlesErrorWhenDeletingOrder()
    {
        $response = $this->actingAs(User::factory()->create())->deleteJson(route('orders.destroy', 'non-existent-id'));

        $response->assertStatus(500)
            ->assertJson([
                'status' => 'error',
                'message' => 'Failed to delete order.',
            ]);
    }
}
