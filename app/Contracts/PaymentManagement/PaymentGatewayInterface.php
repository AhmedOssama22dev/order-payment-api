<?php

namespace App\Contracts\PaymentManagement;

use App\DTOs\PaymentManagement\PaymentRequestDTO;
use App\DTOs\PaymentManagement\PaymentResponseDTO;

interface PaymentGatewayInterface
{
    public function processPayment(): PaymentResponseDTO;
    
}