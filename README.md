# Extendable Order and Payment Management API

## Laravel Project Setup Guide
Follow these instructions to get the project up and running.

---

## Step 1: Clone the Repository

1. Open your terminal or command prompt.
2. Navigate to the directory where you want to clone the project.
3. Run the following command to clone the repository:

   ```bash
   git clone https://github.com/AhmedOssama22dev/order-payment-api
   ```

4. Navigate into the project directory:

   ```bash
   cd order-payment-api
   ```

---

## Step 2: Install PHP Dependencies

1. Install Composer dependencies by running:

   ```bash
   composer install
   ```

   This will download all the required PHP packages listed in `composer.json`.

---

## Step 3: Set Up Environment Variables

1. Copy the `.env.example` file to `.env`:

   ```bash
   cp .env.example .env
   ```

2. Open the `.env` file in a text editor and update the following values:

   - **Database Configuration**:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=your_database_name
     DB_USERNAME=your_database_username
     DB_PASSWORD=your_database_password
     ```

   - **App Configuration**:
     ```env
     APP_NAME=YourAppName
     APP_ENV=local
     APP_KEY=
     APP_DEBUG=true
     APP_URL=http://localhost
     ```

3. Generate the application key:

   ```bash
   php artisan key:generate
   ```
4. Generate JWT key
   
   ```bash
   php artisan jwt:secret
   ```
---

## Step 3: Set Up the Database

1. Create a new database using your preferred database management tool (e.g., phpMyAdmin, MySQL CLI, etc.).
2. Run migrations to create the necessary tables:

   ```bash
   php artisan migrate
   ```

3. (Optional) Seed the database with dummy data:

   ```bash
   php artisan db:seed
   ```

---

## Step 4: Run the Application

1. Start the Laravel development server:

   ```bash
   php artisan serve
   ```

2. Open your browser and navigate to:

   ```
   http://localhost:8000
   ```

   You should see the Laravel welcome page or your application's homepage.

---


## Step 5: Test the Application

1. Run the Laravel test suite to ensure everything is working correctly:

   ```bash
   php artisan test
   ```

2. Manually test the application by making API request from the given postman collection

---




## How to Extend Payment Gateway?
### Adding a New Payment Gateway

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
       public function processPayment($amount): PaymentResponseDTO
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
- Check the `PaymentGatewayEnum.php` to make sure the new one was added.

---

## API Docs [POSTMAN]
[Postman colelction](Documents/Order%20and%20Payment%20Management%20API.postman_collection.json)
## System Design Elements

### ERD  
![ERD](Documents/order-payment-erd.drawio.svg)  

### Architecture Diagram
![Architecture](Documents/order-payment-arch.drawio.svg)