<?php

namespace App\Services;

use App\Mail\PayoutSend;
use App\Models\Merchant;
use Illuminate\Support\Str;
use RuntimeException;
use Mail;
/**
 * You don't need to do anything here. This is just to help
 */
class ApiService
{
    /**
     * Create a new discount code for an affiliate
     *
     * @param Merchant $merchant
     *
     * @return array{id: int, code: string}
     */
    public function createDiscountCode(Merchant $merchant): array
    {
        return [
            'id' => rand(0, 100000),
            'code' => Str::uuid()
        ];
    }

    /**
     * Send a payout to an email
     *
     * @param  string $email
     * @param  float $amount
     * @return voids
     * @throws RuntimeException
     */
    public function sendPayout(string $email, float $amount)
    {
        
    }
}
