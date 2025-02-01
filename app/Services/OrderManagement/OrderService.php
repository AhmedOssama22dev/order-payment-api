<?php

namespace App\Services\OrderManagement;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Contracts\OrderManagement\OrderRepositoryInterface;

class OrderService
{
    protected $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }


    public function createOrder($userId, $products)
    {
        DB::beginTransaction();
        try {
            if (empty($products)) {
                throw new \InvalidArgumentException('Product list cannot be empty.');
            }

            $totalPrice = $this->calculateTotalPrice($products);

            $order = $this->orderRepository->create([
                'user_id' => $userId,
                'total_price' => $totalPrice,
            ]);

            DB::commit();

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getOrder($id)
    {
        return $this->orderRepository->find($id);
    }

    public function getOrdersWithFilters($filters)
    {
        if (isset($filters['status']) && !in_array($filters['status'], ['pending', 'confirmed', 'cancelled'])) {
            throw new \InvalidArgumentException('Invalid status provided.');
        }
        return $this->orderRepository->getWithFilters($filters);
    }

    /**
     * Update an existing order.
     *
     * @param  string  $id
     * @param  array  $data
     * @return \App\Models\Order
     */
    public function updateOrder(string $id, array $data): Order
    {
        DB::beginTransaction();

        try {
            $order = $this->orderRepository->update($id, $data);
            DB::commit();

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function calculateTotalPrice($products)
    {
        return collect($products)->sum(function ($product) {
            return is_numeric($product['price']) && is_numeric($product['quantity'])
                ? $product['price'] * $product['quantity']
                : 0;
        }) ?? 0;
    }


    public function destroyOrder(string $id): bool
    {
        DB::beginTransaction();

        try {
            $order = $this->orderRepository->find($id);

            if (!$order) {
                throw new \Exception('Order not found.');
            }

            if ($order->payment()->exists()) {
                throw new \Exception('Cannot delete order with payment.');
            }

            $isDeleted = $this->orderRepository->delete($id);
            DB::commit();

            return $isDeleted;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
