<?php

namespace App\Console\Commands;

use App\Models\PaymentMethod;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakePaymentGateway extends Command
{
    protected $signature = 'make:payment-gateway {name}';
    protected $description = 'Generate a new payment gateway scaffolding';

    public function handle()
    {
        $name = ucfirst($this->argument('name')); // Ensure proper class naming convention
        $stubPath = base_path('stubs/payment-gateway.stub'); // Define stub file path
        $targetPath = app_path("Services/PaymentManagement/PaymentGateways/{$name}Payment.php");

        (new Filesystem())->ensureDirectoryExists(app_path("Services/PaymentManagement/PaymentGateways"));

        if (file_exists($targetPath)) {
            $this->error("Payment gateway {$name} already exists!");
            return;
        }

        $stub = file_get_contents($stubPath);
        $stub = str_replace('{{gatewayName}}', $name, $stub);
        $stub = str_replace('{{gatewayName | strtolower}}', strtolower($name), $stub);
        file_put_contents($targetPath, $stub);

        $this->updatePaymentConfig($name);

        try {
            PaymentMethod::create([
                'name' => $name,
                'is_active' => true,
            ]);
            $this->info("Payment method {$name} added to the database.");
        }
        catch (\Exception $e) {
            $this->info("Payment method {$name} cannot be added to the database.");
        }

        $this->info("Payment gateway {$name}Payment.php created successfully.");
        $this->info("Configuration for {$name} added to config/payment.php.");
    }

    private function updatePaymentConfig(string $gatewayName)
    {
        $configPath = config_path('payment.php');

        if (!file_exists($configPath)) {
            $this->error("Config file config/payment.php does not exist.");
            return;
        }

        // Read the current config file
        $configContent = file_get_contents($configPath);

        if (strpos($configContent, "'$gatewayName' => [") !== false) {
            $this->error("Gateway $gatewayName already exists in the config file.");
            return;
        }

        $lowerCaseGatewayName = strtolower($gatewayName);
        $upperCaseGatewayName = strtoupper($gatewayName);

        $newConfigEntry = <<<PHP
    ,'$lowerCaseGatewayName' => [
        'client_id' => env(strtoupper('{$upperCaseGatewayName}_CLIENT_ID')),
        'client_secret' => env(strtoupper('{$upperCaseGatewayName}_CLIENT_SECRET')),
        'payment_url' => env(strtoupper('{$upperCaseGatewayName}_PAYMENT_URL')),
    ],
PHP;

        // Find the last array closing bracket in the config file and insert the new config before it
        $updatedConfig = preg_replace('/\];\s*$/', "$newConfigEntry\n];", $configContent);

        if ($updatedConfig) {
            file_put_contents($configPath, $updatedConfig);
        }
    }
}
