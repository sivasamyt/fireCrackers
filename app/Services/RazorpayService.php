<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Razorpay\Api\Api;
use RuntimeException;

class RazorpayService
{
    public function api(): Api
    {
        $key = config('services.razorpay.key');
        $secret = config('services.razorpay.secret');

        if (! $key || ! $secret) {
            throw new RuntimeException('Razorpay keys are not configured.');
        }

        return new Api($key, $secret);
    }

    public function isConfigured(): bool
    {
        return filled(config('services.razorpay.key')) && filled(config('services.razorpay.secret'));
    }

    public function createOrderPayment(Order $order): Payment
    {
        $api = $this->api();
        $amountPaise = (int) round(((float) $order->grand_total) * 100);

        $razorpayOrder = $api->order->create([
            'receipt' => $order->order_number,
            'amount' => $amountPaise,
            'currency' => 'INR',
            'payment_capture' => 1,
        ]);

        return Payment::query()->create([
            'order_id' => $order->id,
            'provider' => 'razorpay',
            'razorpay_order_id' => $razorpayOrder['id'],
            'amount' => $order->grand_total,
            'status' => 'created',
            'raw_payload' => $razorpayOrder->toArray(),
        ]);
    }

    public function verifyAndMarkPaid(Order $order, string $razorpayPaymentId, string $razorpayOrderId, string $signature): Payment
    {
        $api = $this->api();
        $api->utility->verifyPaymentSignature([
            'razorpay_order_id' => $razorpayOrderId,
            'razorpay_payment_id' => $razorpayPaymentId,
            'razorpay_signature' => $signature,
        ]);

        $payment = $order->payment;

        if (! $payment) {
            throw new RuntimeException('Payment record not found for order.');
        }

        $payment->update([
            'razorpay_payment_id' => $razorpayPaymentId,
            'razorpay_order_id' => $razorpayOrderId,
            'razorpay_signature' => $signature,
            'status' => 'paid',
        ]);

        $order->update([
            'payment_status' => 'paid',
            'status' => 'confirmed',
        ]);

        return $payment->fresh();
    }
}
