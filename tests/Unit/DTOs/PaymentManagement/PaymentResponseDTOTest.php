<?php

namespace Tests\Unit\DTOs\PaymentManagement;

use App\DTOs\PaymentManagement\PaymentResponseDTO;
use PHPUnit\Framework\TestCase;

class PaymentResponseDTOTest extends TestCase
{
    public function testConstructorAndProperties()
    {
        $data = [
            'payment_id' => 'pay_123',
            'status' => 'success',
            'payment_method' => 'credit_card',
            'note' => 'Payment processed successfully',
        ];

        $dto = new PaymentResponseDTO($data);

        $this->assertEquals('pay_123', $dto->payment_id);
        $this->assertEquals('success', $dto->status);
        $this->assertEquals('credit_card', $dto->payment_method);
        $this->assertEquals('Payment processed successfully', $dto->note);
    }

    public function testConstructorWithMissingPaymentId()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The "payment_id" key is required.');

        $data = [
            'status' => 'success',
            'payment_method' => 'credit_card',
        ];

        new PaymentResponseDTO($data);
    }

    public function testConstructorWithMissingStatus()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The "status" key is required.');

        $data = [
            'payment_id' => 'pay_123',
            'payment_method' => 'credit_card',
        ];

        new PaymentResponseDTO($data);
    }

    public function testConstructorWithMissingPaymentMethod()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The "payment_method" key is required.');

        $data = [
            'payment_id' => 'pay_123',
            'status' => 'success',
        ];

        new PaymentResponseDTO($data);
    }

    public function testConstructorWithOptionalNote()
    {
        $data = [
            'payment_id' => 'pay_123',
            'status' => 'success',
            'payment_method' => 'credit_card',
        ];

        $dto = new PaymentResponseDTO($data);

        $this->assertEquals('', $dto->note);
    }

    public function testToArrayMethod()
    {
        $data = [
            'payment_id' => 'pay_123',
            'status' => 'success',
            'payment_method' => 'credit_card',
            'note' => 'Payment processed successfully',
        ];

        $dto = new PaymentResponseDTO($data);
        $result = $dto->toArray();

        $this->assertEquals([
            'payment_id' => 'pay_123',
            'status' => 'success',
            'payment_method' => 'credit_card',
            'note' => 'Payment processed successfully',
        ], $result);
    }

    public function testToArrayMethodWithOptionalNote()
    {
        $data = [
            'payment_id' => 'pay_123',
            'status' => 'success',
            'payment_method' => 'credit_card',
        ];

        $dto = new PaymentResponseDTO($data);
        $result = $dto->toArray();

        $this->assertEquals([
            'payment_id' => 'pay_123',
            'status' => 'success',
            'payment_method' => 'credit_card',
            'note' => '',
        ], $result);
    }
}
