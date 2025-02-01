<?php
namespace App\DTOs\PaymentManagement;

class PaymentResponseDTO
{
    public string $payment_id;
    public string $status;
    public string $payment_method;
    public string $note;
    public function __construct(array $data)
    {
        $this->payment_id = $data['payment_id'];
        $this->status = $data['status'];
        $this->payment_method = $data['payment_method'];
        $this->note = $data['note'] ?? '';
    }

    public function toArray(): array
    {
        return [
            'payment_id' => $this->payment_id,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'note' => $this->note
        ];
    }

}