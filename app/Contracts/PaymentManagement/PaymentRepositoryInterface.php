<?php

namespace App\Contracts\PaymentManagement;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;

interface PaymentRepositoryInterface
{
    public function createPayment(array $data): Payment;

    public function updatePaymentStatus(int $id, string $paymentId, string $status): bool;

    public function getWithFilters(array $filters): Collection;

    public function checkPaidOrder($orderId): Collection;

}
