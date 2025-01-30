<?php

namespace Tests\Unit\Repositories\OrderManagement;

use App\Models\Order;
use App\Repositories\OrderManagement\OrderRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class OrderRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private $orderMock;
    private $orderRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderMock = Mockery::mock(Order::class);
        $this->orderRepository = new OrderRepository();


    }

    public function tearDown(): void
    {
        parent::tearDown();

    }
    public function testCreateOrderSuccessfully()
    {
        $user = \App\Models\User::factory()->create();

        $data = [
            'user_id' => $user->id,
            'total_price' => 100,
            'status' => 'pending',
        ];

        $this->orderMock = Mockery::mock(Order::class);
        $this->app->instance(Order::class, $this->orderMock);

        $result = $this->orderRepository->create($data);

        $this->assertInstanceOf(Order::class, $result);
    }


    public function testFindOrderThrowsModelNotFoundException()
    {
        $orderId = '1';

        $this->instance(Order::class, $this->orderMock);

        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage("Order with ID {$orderId} not found.");

        $this->orderRepository->find($orderId);
    }

    public function testUpdateOrderThrowsModelNotFoundException()
    {
        $orderId = '1';
        $data = ['status' => 'confirmed'];

        $this->instance(Order::class, $this->orderMock);

        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage("Order with ID {$orderId} not found.");

        $this->orderRepository->update($orderId, $data);
    }

    public function testDeleteOrderThrowsModelNotFoundException()
    {
        $orderId = '1';

        $this->instance(Order::class, $this->orderMock);

        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage("Order with ID {$orderId} not found.");

        $this->orderRepository->delete($orderId);
    }
}
