<?php

namespace App\Services\PaymentManagement;

use InvalidArgumentException;
use App\Contracts\PaymentManagement\PaymentGatewayInterface;
use App\Services\PaymentManagement\PaymentGateways\PayPalPayment;
use App\Services\PaymentManagement\PaymentGateways\CreditCardPayment;
use App\Enums\PaymentGatewayEnum;

class PaymentGatewayFactory
{
    protected array $gateways;

    public function __construct(array $gateways = [])
    {
        $this->gateways = $gateways;
    }
    public function createGateway(string $gateway): PaymentGatewayInterface
    {
        $gateways = $this->gateways ?: PaymentGatewayEnum::all();
        if (!isset($gateways[$gateway])) {
            throw new InvalidArgumentException("Unsupported payment gateway: $gateway");
        }

        return app($gateways[$gateway]);
    }
}
