<?php

namespace App\Http\Controllers\OrderManagement;

use Illuminate\Http\Request;
use App\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\OrderManagement\OrderService;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        try {
            $orders = $this->orderService->getOrdersWithFilters($request->all());
            return ApiResponse::success('Orders fetched successfully', $orders);
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to fetch orders.', 500, $e->getMessage());
        }

    }

    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'products' => 'required|array',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Validation failed.', 422, $validator->errors());
        }

        try {
            $this->orderService->createOrder($request->user_id, $request->products);

            return ApiResponse::success('Order created successfully!', null,201);
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to create order. Please try again.', 500, $e->getMessage());
        }
    }

    public function show(string $id)
    {
        try {
            $order = $this->orderService->getOrder($id);
            return ApiResponse::success('Order fetched successfully!', $order);
        } catch (\Exception $e) {
            return ApiResponse::error('Order not found.', 404);
        }
    }


    public function update(Request $request, string $id)
    {
        try {
            $order = $this->orderService->updateOrder($id, $request->all());
            return ApiResponse::success('Order updated successfully!', $order);
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to update order.', 500, $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $this->orderService->destroyOrder($id);
            return ApiResponse::success('Order deleted successfully!',null, 204);
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to delete order.', 500, $e->getMessage());
        }
    }
}
