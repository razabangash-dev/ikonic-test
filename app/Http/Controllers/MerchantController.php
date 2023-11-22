<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\Order;
use App\Services\MerchantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MerchantController extends Controller
{
    public function __construct(
        MerchantService $merchantService
    ) {
    }

    /**
     * Useful order statistics for the merchant API.
     * 
     * @param Request $request Will include a from and to date
     * @return JsonResponse Should be in the form {count: total number of orders in range, commission_owed: amount of unpaid commissions for orders with an affiliate, revenue: sum order subtotals}
     */
    public function orderStats(Request $request): JsonResponse
    {
        // TODO: Complete this method

        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after:from',
        ]);

        $fromDate = Carbon::parse($request->input('from'));
        $toDate = Carbon::parse($request->input('to'));

        try {

            $orderCount = Order::whereBetween('created_at', [$fromDate, $toDate])->count();
            $unpaidCommissions = Order::where('payout_status', Order::STATUS_UNPAID)->where('affiliate_id','!=',null)
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->sum('commission_owed');
            $revenue = Order::whereBetween('created_at', [$fromDate, $toDate])->sum('subtotal');
            return response()->json([
                'count' => $orderCount,
                'commissions_owed' => $unpaidCommissions,
                'revenue' => $revenue,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve order statistics'], 500);
        }
    }
}
