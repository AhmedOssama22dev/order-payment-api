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
        if (!isset($data['payment_id'])) {
            throw new \InvalidArgumentException('The "payment_id" key is required.');
        }
        if (!isset($data['status'])) {
            throw new \InvalidArgumentException('The "status" key is required.');
        }
        if (!isset($data['payment_method'])) {
            throw new \InvalidArgumentException('The "payment_method" key is required.');
        }

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