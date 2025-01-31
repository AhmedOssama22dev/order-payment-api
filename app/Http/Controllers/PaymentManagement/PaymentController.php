<?php

namespace App\Http\Controllers\PaymentManagement;

use Illuminate\Http\Request;
use App\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\DTOs\PaymentManagement\PaymentRequestDTO;
use App\Services\PaymentManagement\PaymentService;
use App\Http\Requests\Requests\PaymentManagement\ProcessPaymentRequest;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request)
    {
        try {
            $orders = $this->paymentService->getPaymentsWithFilters($request->all());
            return ApiResponse::success('Payments fetched successfully', $orders);
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to fetch payments.', 500, $e->getMessage());
        }
    }
    public function processPayment(ProcessPaymentRequest $request)
    {
        try {
            $paymentDTO = PaymentRequestDTO::fromRequest($request->all());
            $paymentResponseDTO = $this->paymentService->process($paymentDTO);
            return ApiResponse::success('Payment processed successfully', $paymentResponseDTO->toArray());
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to process payment.', 500, $e->getMessage());
        }
    }
}
