<?php
namespace App\DTOs\PaymentManagement;

class PaymentRequestDTO
{
    public int $orderId;
    public string $payment_method;

    public function __construct(array $data)
    {
        if (!isset($data['order_id'])) {
            throw new \InvalidArgumentException('The "order_id" key is required.');
        }
        if (!isset($data['payment_method'])) {
            throw new \InvalidArgumentException('The "payment_method" key is required.');
        }

        $this->orderId = $data['order_id'];
        $this->payment_method = $data['payment_method'];
    }

    public static function fromRequest(array $data): self
    {
        return new self($data);
    }
}
