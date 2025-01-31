<?php

namespace App\Services\PaymentManagement;

use Exception;
use App\DTOs\PaymentManagement\PaymentRequestDTO;
use App\DTOs\PaymentManagement\PaymentResponseDTO;
use App\Repositories\OrderManagement\OrderRepository;
use App\Repositories\PaymentManagement\PaymentRepository;

class PaymentService
{
    protected array $gateways = [
        'paypal' => \App\Services\PaymentManagement\PaymentGateways\PayPalPayment::class,
        'credit_card' => \App\Services\PaymentManagement\PaymentGateways\CreditCardPayment::class,
    ];

    protected PaymentRepository $paymentRepository;
    protected OrderRepository $orderRepository;

    public function __construct(PaymentRepository $paymentRepository, OrderRepository $orderRepository)
    {
        $this->paymentRepository = $paymentRepository;
        $this->orderRepository = $orderRepository;
    }

    public function process(PaymentRequestDTO $paymentDTO): PaymentResponseDTO
    {
        if (!isset($this->gateways[$paymentDTO->payment_method])) {
            throw new Exception("Payment method not supported.");
        }
        
        $order = $this->orderRepository->find($paymentDTO->orderId);
        if($order->status !== 'confirmed') {
            throw new Exception("Order not yet confirmed.");
        }

        // if there is a payment with the same order_id and status is not failed, return the payment
        $existingPayment = $this->paymentRepository->checkPaidOrder($paymentDTO->orderId)->first();
        
        if($existingPayment) {
            return new PaymentResponseDTO([
                'payment_id' => $existingPayment->payment_id,
                'status' => $existingPayment->payment_status,
                'payment_method' => $existingPayment->payment_method
            ]);
        }
        
        $payment = $this->paymentRepository->createPayment([
            'order_id' => $paymentDTO->orderId,
            'payment_method' => $paymentDTO->payment_method,
            'status' => 'pending'
        ]);
        
        if(!$payment) {
            throw new Exception("Failed to create payment.");
        }

        $gateway = new $this->gateways[$paymentDTO->payment_method]();
        $response = $gateway->processPayment($paymentDTO);
        $this->paymentRepository->updatePaymentStatus($payment->id, $response->payment_id, $response->status);

        return $response;
    }

    public function getPaymentsWithFilters($filters) 
    {
        if (isset($filters['status']) && !in_array($filters['status'], ['pending', 'paid', 'failed'])) {
            throw new \InvalidArgumentException('Invalid status provided.');
        }
        
        return $this->paymentRepository->getWithFilters($filters);
    }
}
