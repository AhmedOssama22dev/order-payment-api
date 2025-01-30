<?php

namespace Tests\Unit\Services\OrderManagement;

use App\Services\OrderManagement\OrderService;
use App\Contracts\OrderManagement\OrderRepositoryInterface;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    private $orderRepositoryMock;
    private $orderService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orderRepositoryMock = Mockery::mock(OrderRepositoryInterface::class);
        $this->orderService = new OrderService($this->orderRepositoryMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testCreateOrderSuccessfully()
    {
        $userId = 1;
        $products = [
            ['price' => 10, 'quantity' => 2],
            ['price' => 20, 'quantity' => 1],
        ];
        $totalPrice = 40; // (10 * 2) + (20 * 1)

        $orderData = [
            'user_id' => $userId,
            'total_price' => $totalPrice,
        ];

        $orderMock = Mockery::mock(Order::class);

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();
        DB::shouldReceive('rollBack')->never();

        $this->orderRepositoryMock
            ->shouldReceive('create')
            ->with($orderData)
            ->andReturn($orderMock);

        $result = $this->orderService->createOrder($userId, $products);

        $this->assertInstanceOf(Order::class, $result);
    }

    public function testCreateOrderThrowsExceptionWhenProductListIsEmpty()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Product list cannot be empty.');

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('rollBack')->once();

        $this->orderService->createOrder(1, []);
    }

    public function testGetOrderSuccessfully()
    {
        $orderId = 1;
        $orderMock = Mockery::mock(Order::class);

        $this->orderRepositoryMock
            ->shouldReceive('find')
            ->with($orderId)
            ->andReturn($orderMock);

        $result = $this->orderService->getOrder($orderId);

        $this->assertInstanceOf(Order::class, $result);
    }

    public function testGetOrdersWithFiltersSuccessfully()
    {
        $filters = ['status' => 'confirmed'];
        $orderCollection = collect([
            Mockery::mock(Order::class),
            Mockery::mock(Order::class),
        ]);

        $this->orderRepositoryMock
            ->shouldReceive('getWithFilters')
            ->with($filters)
            ->andReturn(new \Illuminate\Database\Eloquent\Collection($orderCollection));

        $result = $this->orderService->getOrdersWithFilters($filters);

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
        $this->assertCount(2, $result);
    }

    public function testGetOrdersWithFiltersThrowsExceptionForInvalidStatus()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid status provided.');

        $filters = ['status' => 'invalid_status'];

        $this->orderService->getOrdersWithFilters($filters);
    }

    public function testUpdateOrderSuccessfully()
    {
        $orderId = 1;
        $data = ['status' => 'confirmed'];
        $orderMock = Mockery::mock(Order::class);

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();
        DB::shouldReceive('rollBack')->never();

        $this->orderRepositoryMock
            ->shouldReceive('update')
            ->with($orderId, $data)
            ->andReturn($orderMock);

        $result = $this->orderService->updateOrder($orderId, $data);

        $this->assertInstanceOf(Order::class, $result);
    }

    public function testUpdateOrderThrowsException()
    {
        $this->expectException(\Exception::class);

        $orderId = 1;
        $data = ['status' => 'confirmed'];

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('rollBack')->once();

        $this->orderRepositoryMock
            ->shouldReceive('update')
            ->with($orderId, $data)
            ->andThrow(new \Exception('Update failed'));

        $this->orderService->updateOrder($orderId, $data);
    }

    public function testDestroyOrderSuccessfully()
    {
        $orderId = 1;
        $orderMock = Mockery::mock(Order::class);
        $orderMock->shouldReceive('payment->exists')->andReturn(false);

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();
        DB::shouldReceive('rollBack')->never();

        $this->orderRepositoryMock
            ->shouldReceive('find')
            ->with($orderId)
            ->andReturn($orderMock);

        $this->orderRepositoryMock
            ->shouldReceive('delete')
            ->with($orderId)
            ->andReturn(true);

        $result = $this->orderService->destroyOrder($orderId);

        $this->assertTrue($result);
    }
    public function testDestroyOrderReturnsFalseWhenOrderNotFound()
    {
        $orderId = 1;

        $this->orderRepositoryMock
            ->shouldReceive('find')
            ->with($orderId)
            ->andThrow(new \Illuminate\Database\Eloquent\ModelNotFoundException("Order with ID {$orderId} not found."));

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('rollBack')->once();

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->expectExceptionMessage("Order with ID {$orderId} not found.");

        $this->orderService->destroyOrder($orderId);
    }

    public function testDestroyOrderReturnsFalseWhenPaymentExists()
    {
        $orderId = 1;
        $orderMock = Mockery::mock(Order::class);
        $orderMock->shouldReceive('payment->exists')->andReturn(true);

        $this->orderRepositoryMock
            ->shouldReceive('find')
            ->with($orderId)
            ->andReturn($orderMock);

        $this->orderRepositoryMock
            ->shouldNotReceive('delete');

        $result = $this->orderService->destroyOrder($orderId);

        $this->assertFalse($result);
    }

    public function testDestroyOrderThrowsException()
    {
        $this->expectException(\Exception::class);

        $orderId = 1;
        $orderMock = Mockery::mock(Order::class);
        $orderMock->shouldReceive('payment->exists')->andReturn(false);

        $this->orderRepositoryMock
            ->shouldReceive('find')
            ->with($orderId)
            ->andReturn($orderMock);

        $this->orderRepositoryMock
            ->shouldReceive('delete')
            ->with($orderId)
            ->andThrow(new \Exception('Deletion failed'));

        $this->orderService->destroyOrder($orderId);
    }

    public function testCalculateTotalPriceReturnsCorrectValue()
    {
        $products = [
            ['price' => 10, 'quantity' => 2],
            ['price' => 20, 'quantity' => 1],
        ];

        $totalPrice = $this->orderService->calculateTotalPrice($products);

        $this->assertEquals(40, $totalPrice);
    }

    public function testCalculateTotalPriceReturnsZeroForInvalidProducts()
    {
        $products = [
            ['price' => 'invalid', 'quantity' => 2],
            ['price' => 20, 'quantity' => 'invalid'],
        ];

        $totalPrice = $this->orderService->calculateTotalPrice($products);

        $this->assertEquals(0, $totalPrice);
    }
}
