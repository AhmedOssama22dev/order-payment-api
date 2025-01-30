<?php
namespace App\Repositories\OrderManagement;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Contracts\OrderManagement\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    /**
     * @param  array  $data
     * @return \App\Models\Order
     */
    public function create(array $data): Order
    {
        $order = new Order();
        $order->fill($data); // fill fillable fields
        $order->user_id = $data['user_id']; // guarded field
        $order->total_price = $data['total_price']; // guarded field
        $order->save();
        
        return $order;
    }

    /**
     * Get orders with filters like status, limit, and offset.
     *
     * @param  array  $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWithFilters(array $filters): Collection
    {
        $query = Order::query();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $limit = $filters['limit'] ?? 10;
        $offset = $filters['offset'] ?? 0;

        return $query->skip($offset)->take($limit)->get();
    }


    /**
     * @param  string  $id
     * @return \App\Models\Order
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find(string $id): Order
    {
        $order = Order::find($id);

        if (!$order) {
            throw new ModelNotFoundException("Order with ID {$id} not found.");
        }

        return $order;
    }

    /**
     * @param  string  $id
     * @param  array  $data
     * @return \App\Models\Order
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(string $id, array $data): Order
    {
        $order = $this->find($id);
        if (!$order) {
            throw new ModelNotFoundException("Order with ID {$id} not found.");
        }

        $order->update($data);

        return $order;
    }

    /**
     * @param  string  $id
     * @return bool
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete(string $id): bool
    {
        $order = $this->find($id);
        if (!$order) {
            throw new ModelNotFoundException("Order with ID {$id} not found.");
        }
        return $order->delete();
    }
}
