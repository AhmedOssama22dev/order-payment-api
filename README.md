# Extendable Order and Payment Management API

## Setup Instructions

## How to Extend Payment Gateway?
# Adding a New Payment Gateway

This guide explains how to add a new payment gateway to the system using the existing scaffolding command.

## Steps to Add a New Payment Gateway

### 1. Run the Payment Gateway Scaffolding Command
To generate a new payment gateway class and update the configuration file, run the following command:
```bash
php artisan make:payment-gateway <GatewayName>
```
Replace `<GatewayName>` with the actual payment gateway name (e.g., `Stripe`, `PayMob`).

### 2. Implement the Payment Gateway Class
After running the command, a new class will be created inside `app/Services/PaymentManagement/PaymentGateways/`.

1. Open the newly created file:  
   ```php
   app/Services/PaymentManagement/PaymentGateways/<GatewayName>Payment.php
   ```
2. Implement the `PaymentGatewayInterface` methods inside the class. Example:
   ```php
   namespace App\Services\PaymentManagement\PaymentGateways;
   
   use App\DTOs\PaymentManagement\PaymentRequestDTO;
   use App\DTOs\PaymentManagement\PaymentResponseDTO;
   use App\Services\PaymentManagement\PaymentGatewayInterface;

   class <GatewayName>Payment implements PaymentGatewayInterface
   {
       public function processPayment(PaymentRequestDTO $request): PaymentResponseDTO
       {
           // Implement API call to <GatewayName> here

           return new PaymentResponseDTO([
               'payment_id' => $response['payment_id'],
               'status' => $response['status'],
               'payment_method' => '<gateway_name>',
           ]);
       }
   }
   ```

### 3. Configure the Payment Gateway
The command will automatically update `config/payment.php` with the new gateway entry.Feel free to modify these names if required but you need some changes in the gatewayInterface implementation. Verify the new entry in:
```php
return [
    '<gateway_name>' => [
        'client_id' => env('<GATEWAY_NAME>_CLIENT_ID', ''),
        'client_secret' => env('<GATEWAY_NAME>_CLIENT_SECRET',''),
        'payment_url' => env('<GATEWAY_NAME>_PAYMENT_URL', ''),
    ],
];
```

Update your `.env` file with the required credentials:
```env
<GATEWAY_NAME>_CLIENT_ID=your_client_id
<GATEWAY_NAME>_CLIENT_SECRET=your_client_secret
<GATEWAY_NAME>_PAYMENT_URL=https://api.gateway.com/payment
```

### 4. Clear and Cache Configuration
To ensure Laravel loads the updated payment configuration, run:
```bash
php artisan config:clear
php artisan config:cache
```

### 5. Use the Payment Gateway
The new payment gateway is now available inside `PaymentService`. 
You can also check the PaymentGatewayEnum.php to make sure the new one was added.

Pass only the `payment_method` name and `order_id` in the request body of the process payment API:

#### Example API Request:
```json
{
    "payment_method": "<gateway_name>",
    "order_id": "1"
}
```


### Notes
- Ensure your new gateway class follows the `PaymentGatewayInterface` structure.
- If you modify `config/payment.php` manually, always run `php artisan config:cache`.
- The `PaymentService` will automatically detect the new gateway based on the config.

---

## API Docs [POSTMAN]

## System Design Elements

### ERD  
![ERD](Documents/order-payment-erd.drawio.svg)  

### Architecture Diagram
![Architecture](Documents/order-payment-arch.drawio.svg)