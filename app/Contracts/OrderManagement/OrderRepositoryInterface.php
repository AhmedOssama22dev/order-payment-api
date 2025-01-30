<?php

namespace App\Contracts\OrderManagement;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface
{
    public function create(array $data): Order;
    public function getWithFilters(array $filters): Collection;
    public function find(string $id): Order;
    public function update(string $id, array $data): Order;
    public function delete(string $id): bool;
}
