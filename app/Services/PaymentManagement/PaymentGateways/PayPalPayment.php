<?php

namespace App\Services\PaymentManagement\PaymentGateways;

use App\DTOs\PaymentManagement\PaymentRequestDTO;
use App\DTOs\PaymentManagement\PaymentResponseDTO;
use App\Contracts\PaymentManagement\PaymentGatewayInterface;

class PayPalPayment implements PaymentGatewayInterface
{
    protected $paypalConfig;
    protected $apiKey;
    protected $apiSecret;
    protected $paymentUrl;
    protected $httpClient;

    public function __construct()
    {
        $this->httpClient = new \GuzzleHttp\Client();
        // Get paypal api configurations
        $paypalConfig = config('payment.paypal');

        $this->apiKey = $paypalConfig['client_id'];
        $this->apiSecret = $paypalConfig['client_secret'];
        $this->paymentUrl = $paypalConfig['payment_url'];

    }
    public function processPayment(): PaymentResponseDTO
    {
        // dummy api call
        // post request to paypal api
        // $response = $this->httpClient->post($this->paymentUrl, [
        //     'headers' => [
        //         'Authorization' => 'Bearer ' . self::getAPIToken(),
        //         'Content-Type' => 'application/json',
        //         'API-Key' => $this->apiKey,
        //         'API-Secret' => $this->apiSecret,
        //     ],
        // ]);

        // This is a dummy response
        $response = [
            'payment_id' => uniqid('paypal', true),
            'status' => 'approved',
        ]; // This should be extracted from the response of the API call

        $status = $this->mapPaymentStatus(strtolower($response['status'])); // map the status to the standard status

        return new PaymentResponseDTO([
            'payment_id' => $response['payment_id'],
            'status' => $status,
            'payment_method' => 'paypal',
        ]);
    }

    public static function getAPIToken()
    {
        // dummy api call
        // post request to paypal api
        // $response = $this->httpClient->post($this->paymentUrl, [
        //     'headers' => [
        //         'Authorization' => 'Bearer ' . self::getAPIToken(),
        //         'Content-Type' => 'application/json',
        //         'API-Key' => $this->apiKey,
        //         'API-Secret' => $this->apiSecret,
        //     ],
        // ]);

        return $response->data['access_token'] ?? '';
    }
    
    protected function mapPaymentStatus($status)
    {
        switch ($status) {
            case 'approved':
                return 'paid';
            case 'pending':
                return 'pending';
            case 'failed':
                return 'failed';
            default:
                return 'failed';
        }
    }
}