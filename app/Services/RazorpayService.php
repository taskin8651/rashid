<?php

namespace App\Services;

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class RazorpayService
{
    protected Api $api;

    public function __construct()
    {
        $this->api = new Api(config('services.razorpay.key_id'), config('services.razorpay.key_secret'));
    }

    public function createOrder(float $amountRupees, string $receipt): array
    {
        $order = $this->api->order->create([
            'amount' => (int) round($amountRupees * 100),
            'currency' => 'INR',
            'receipt' => $receipt,
        ]);

        return [
            'order_id' => $order['id'],
            'amount' => $order['amount'],
            'currency' => $order['currency'],
            'key_id' => config('services.razorpay.key_id'),
        ];
    }

    public function verifySignature(string $orderId, string $paymentId, string $signature): bool
    {
        try {
            $this->api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $orderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => $signature,
            ]);
            return true;
        } catch (SignatureVerificationError $e) {
            return false;
        }
    }
}
