<?php

namespace App\Services;

use App\Exceptions\AffiliateCreateException;
use App\Mail\AffiliateCreated;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Mail;


class AffiliateService
{
    public function __construct(
        protected ApiService $apiService
    ) {
    }

    /**
     * Create a new affiliate for the merchant with the given commission rate.
     *
     * @param  Merchant $merchant
     * @param  string $email
     * @param  string $name
     * @param  float $commissionRate
     * @return Affiliate
     */
    public function register(Merchant $merchant, string $email, string $name, float $commissionRate): Affiliate
    {
        // TODO: Complete this method

        // Check if the affiliate already exists with the given email
        $existingAffiliate = User::where('email', $email)->first();

        if ($existingAffiliate) {
            throw new AffiliateCreateException("Affiliate with email {$email} already exists.");
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt('abcd123'),
            'type' => User::TYPE_AFFILIATE,
        ]);

        // Create a new affiliate associated with the user and merchant
        $affiliate = Affiliate::create([
            'user_id' => $user->id,
            'merchant_id' => $merchant->id,
            'commission_rate' => $commissionRate,
            'discount_code' => $this->apiService->createDiscountCode($merchant)['code'],

        ]);

        // Send an email notification to the affiliate
        Mail::to($email)->send(new AffiliateCreated($affiliate));

        return $affiliate;

    }
}

