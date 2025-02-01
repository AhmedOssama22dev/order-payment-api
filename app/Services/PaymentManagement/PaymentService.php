<?php

namespace App\Services\PaymentManagement;

use Exception;
use App\DTOs\PaymentManagement\PaymentRequestDTO;
use App\DTOs\PaymentManagement\PaymentResponseDTO;
use App\Repositories\OrderManagement\OrderRepository;
use App\Repositories\PaymentManagement\PaymentRepository;
use App\Services\PaymentManagement\PaymentGatewayFactory;

class PaymentService
{
    protected PaymentRepository $paymentRepository;
    protected OrderRepository $orderRepository;
    protected PaymentGatewayFactory $gatewayFactory;

    public function __construct(
        PaymentRepository $paymentRepository,
        OrderRepository $orderRepository,
        PaymentGatewayFactory $gatewayFactory
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->orderRepository = $orderRepository;
        $this->gatewayFactory = $gatewayFactory;
    }

    public function process(PaymentRequestDTO $paymentDTO): PaymentResponseDTO
    {
        // Fetch the order and ensure it's confirmed
        $order = $this->orderRepository->find($paymentDTO->orderId);
        if (!$order || $order->status !== 'confirmed') {
            throw new Exception("Order not yet confirmed.");
        }

        $amount = $order->total_amount ?? 0;

        // Check if a valid payment already exists
        $existingPayment = $this->paymentRepository->checkPaidOrder($paymentDTO->orderId)->first();
        if ($existingPayment) {
            return new PaymentResponseDTO([
                'payment_id' => $existingPayment->payment_id,
                'status' => $existingPayment->payment_status,
                'payment_method' => $existingPayment->payment_method,
                'note' => 'Payment already exists for this order.'
            ]);
        }

        // Create a pending payment record
        $payment = $this->paymentRepository->createPayment([
            'order_id' => $paymentDTO->orderId,
            'payment_method' => $paymentDTO->payment_method,
            'status' => 'pending'
        ]);

        if (!$payment) {
            throw new Exception("Failed to create payment.");
        }

        // Get the correct payment gateway using the factory
        $gateway = $this->gatewayFactory->createGateway($paymentDTO->payment_method);
        $response = $gateway->processPayment($amount);

        // Update the payment status in the database
        $this->paymentRepository->updatePaymentStatus($payment->id, $response->payment_id, $response->status);

        return $response;
    }

    public function getPaymentsWithFilters(array $filters)
    {
        if (isset($filters['status']) && !in_array($filters['status'], ['pending', 'paid', 'failed'])) {
            throw new \InvalidArgumentException('Invalid status provided.');
        }

        return $this->paymentRepository->getWithFilters($filters);
    }
}
