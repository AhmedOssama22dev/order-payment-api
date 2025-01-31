<?php
namespace App\DTOs\PaymentManagement;

class PaymentRequestDTO {
    public int $orderId;
    public string $payment_method;

    public function __construct(array $data)
    {
        $this->orderId = $data['order_id'];
        $this->payment_method = $data['payment_method'];
    }

    public static function fromRequest(array $data): self
    {
        return new self($data);
    }
}
