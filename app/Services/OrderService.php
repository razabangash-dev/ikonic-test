<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Str;
use Carbon\Carbon;
use Log;

class OrderService
{
    public function __construct(
        protected AffiliateService $affiliateService
    ) {
    }

    /**
     * Process an order and log any commissions.
     * This should create a new affiliate if the customer_email is not already associated with one.
     * This method should also ignore duplicates based on order_id.
     *
     * @param  array{order_id: string, subtotal_price: float, merchant_domain: string, discount_code: string, customer_email: string, customer_name: string} $data
     * @return void
     */
    public function processOrder(array $data)
    {
        // TODO: Complete this method
        $existingOrder = Order::where('external_order_id', $data['order_id'])->first();
        if ($existingOrder) {
            return;
        }

        DB::beginTransaction();
        try {

            $merchant = Merchant::where('domain', $data['merchant_domain'])->first();
            $affiliate = (new AffiliateService(new ApiService()))->register($merchant, $data['customer_email'], $data['customer_name'], 0.1);

            $order = Order::create([
                'merchant_id' => $merchant->id,
                'affiliate_id' => $affiliate->id,
                'subtotal' => $data['subtotal_price'],
                'commission_owed' => $data['subtotal_price'] * $affiliate->commission_rate,
                'payout_status' => Order::STATUS_UNPAID,
                'created_at' => Carbon::now(),
                'external_order_id' => Str::uuid()
            ]);

            Log::info("Commission logged :{$order->commission_owed}");

            DB::commit();

        } catch (\Exception $e) {

            DB::rollBack();

            throw $e;
        }




    }
}

