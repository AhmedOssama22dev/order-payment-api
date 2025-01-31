<?php

namespace App\Services\PaymentManagement\PaymentGateways;

use App\DTOs\PaymentManagement\PaymentRequestDTO;
use App\DTOs\PaymentManagement\PaymentResponseDTO;
use App\Contracts\PaymentManagement\PaymentGatewayInterface;

class CreditCardPayment implements PaymentGatewayInterface
{
    public function processPayment(): PaymentResponseDTO
    {
        return new PaymentResponseDTO([
            'payment_id' => uniqid('credit_card', true),
            'status' => 'pending',
            'payment_method' => 'credit_card',
        ]);
    }

    
}