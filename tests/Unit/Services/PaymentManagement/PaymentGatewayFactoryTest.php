<?php

namespace Tests\Unit\Services\PaymentManagement;

use App\DTOs\PaymentManagement\PaymentResponseDTO;
use Tests\TestCase;
use App\Services\PaymentManagement\PaymentGatewayFactory;
use App\Contracts\PaymentManagement\PaymentGatewayInterface;
use App\Enums\PaymentGatewayEnum;
use InvalidArgumentException;

class PaymentGatewayFactoryTest extends TestCase
{
    protected array $gateways = [
        'gateway1' => FakePaymentGateway1::class,
        'gateway2' => FakePaymentGateway2::class,
    ];

    public function testItCreatesAGateway()
    {
        $factory = new PaymentGatewayFactory($this->gateways);
        $gateway = $factory->createGateway('gateway1');

        $this->assertInstanceOf(PaymentGatewayInterface::class, $gateway);
    }

    public function testItCreatesAnotherGateway()
    {
        $factory = new PaymentGatewayFactory($this->gateways);
        $gateway = $factory->createGateway('gateway2');

        $this->assertInstanceOf(PaymentGatewayInterface::class, $gateway);
    }

    public function testItThrowsExceptionForUnsupportedGateway()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported payment gateway: unknown_gateway');

        $factory = new PaymentGatewayFactory($this->gateways);
        $factory->createGateway('unknown_gateway');
    }
}


// Fake payment gateway implementations for testing
class FakePaymentGateway1 implements PaymentGatewayInterface
{
    public function processPayment($amount = 0): PaymentResponseDTO
    {
        return new PaymentResponseDTO([
            'payment_id' => 'fake_payment_id',
            'status' => 'paid',
            'payment_method' => 'fake_payment_method',
        ]);
    }
}

class FakePaymentGateway2 implements PaymentGatewayInterface
{
    public function processPayment($amount = 0): PaymentResponseDTO
    {
        return new PaymentResponseDTO([
            'payment_id' => 'fake_payment_id_2',
            'status' => 'paid',
            'payment_method' => 'fake_payment_method_2',
        ]);
    }
}
