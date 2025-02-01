<?php

namespace Tests\Unit\DTOs\PaymentManagement;

use App\DTOs\PaymentManagement\PaymentRequestDTO;
use PHPUnit\Framework\TestCase;

class PaymentRequestDTOTest extends TestCase
{
    public function testConstructorAndGetters()
    {
        $data = [
            'order_id' => 123,
            'payment_method' => 'credit_card',
        ];

        $dto = new PaymentRequestDTO($data);

        $this->assertEquals(123, $dto->orderId);
        $this->assertEquals('credit_card', $dto->payment_method);
    }

    public function testFromRequestMethod()
    {
        $data = [
            'order_id' => 456,
            'payment_method' => 'paypal',
        ];

        $dto = PaymentRequestDTO::fromRequest($data);

        $this->assertInstanceOf(PaymentRequestDTO::class, $dto);
        $this->assertEquals(456, $dto->orderId);
        $this->assertEquals('paypal', $dto->payment_method);
    }

    public function testConstructorWithMissingOrderId()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The "order_id" key is required.');

        $data = [
            'payment_method' => 'credit_card',
        ];

        new PaymentRequestDTO($data);
    }

    public function testConstructorWithMissingPaymentMethod()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The "payment_method" key is required.');

        $data = [
            'order_id' => 123,
        ];

        new PaymentRequestDTO($data);
    }
}
