<?php

namespace App\Http\Controllers;

use App\Services\AffiliateService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    /**
     * Pass the necessary data to the process order method
     * 
     * @param  Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        // TODO: Complete this method
 
        $data =  $request->validate([
            'order_id' => 'required|string',
            'subtotal_price' => 'required|numeric',
            'merchant_domain' => 'required|string',
            'discount_code' => 'nullable|string',
        ]);


        try {
            // Process the order using the OrderService
            $this->orderService->processOrder($data);

            // Order processed successfully, return a success response
            return response()->json(['message' => 'Order processed successfully'], 200);
        } catch (\Exception $e) {
            // Handle the exception (log or rethrow)
            return response()->json(['error' => 'Order processing failed'], 500);
        }
    }
}
