<?php

namespace App\Enums;

enum PaymentGatewayEnum: string
{
    case PAYPAL = 'paypal';
    case CREDIT_CARD = 'credit_card';

    public function getClass(): string
    {
        return match ($this) {
            self::PAYPAL => \App\Services\PaymentManagement\PaymentGateways\PayPalPayment::class,
            self::CREDIT_CARD => \App\Services\PaymentManagement\PaymentGateways\CreditCardPayment::class,
        };
    }

    public static function all(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn ($case) => $case->getClass(), self::cases())
        );
    }
}
