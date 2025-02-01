<?php

namespace App\Repositories\PaymentManagement;
use App\Models\Payment;
use App\Contracts\PaymentManagement\PaymentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function createPayment(array $data): Payment
    {
        return Payment::create($data);
    }

    public function updatePaymentStatus(int $id, string $paymentId, string $status): bool
    {
        return Payment::where('id', $id)->update([
            'payment_status' => $status,
            'payment_id' => $paymentId,
        ]) > 0;
    }

    public function getWithFilters($filters): Collection
    {
        $query = Payment::query();

        if (isset($filters['status'])) {
            $query->where('payment_status', $filters['status']);
        }
        if (isset($filters['order_id'])) {
            $query->where('order_id', $filters['order_id']);
        }

        $limit = $filters['limit'] ?? 10;
        $offset = $filters['offset'] ?? 0;

        return $query->skip($offset)->take($limit)->get();
    }

    public function checkPaidOrder($orderId): Collection 
    {
        return Payment::where('order_id', $orderId)
            ->where('payment_status', '=', 'paid')->get();
    }
}