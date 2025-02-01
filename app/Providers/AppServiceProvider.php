<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PaymentManagement\PaymentService;
use App\Services\PaymentManagement\PaymentGatewayFactory;
use App\Contracts\OrderManagement\OrderRepositoryInterface;
use App\Contracts\PaymentManagement\PaymentRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            OrderRepositoryInterface::class,
            \App\Repositories\OrderManagement\OrderRepository::class
        );
        $this->app->bind(
            PaymentRepositoryInterface::class,
            \App\Repositories\PaymentManagement\PaymentRepository::class
        );

        // PaymentService binding
        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService(
                $app->make(PaymentRepositoryInterface::class),
                $app->make(OrderRepositoryInterface::class),
                $app->make(PaymentGatewayFactory::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
