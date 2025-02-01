<?php

namespace Tests\Unit\Enums;

use App\Enums\PaymentGatewayEnum;
use PHPUnit\Framework\TestCase;

class PaymentGatewayEnumTest extends TestCase
{
    public function testEnumCases()
    {
        $this->assertEquals('paypal', PaymentGatewayEnum::PAYPAL->value);
        $this->assertEquals('credit_card', PaymentGatewayEnum::CREDIT_CARD->value);
    }

    public function testGetClassMethod()
    {
        $this->assertEquals(
            \App\Services\PaymentManagement\PaymentGateways\PayPalPayment::class,
            PaymentGatewayEnum::PAYPAL->getClass()
        );

        $this->assertEquals(
            \App\Services\PaymentManagement\PaymentGateways\CreditCardPayment::class,
            PaymentGatewayEnum::CREDIT_CARD->getClass()
        );
    }
}
