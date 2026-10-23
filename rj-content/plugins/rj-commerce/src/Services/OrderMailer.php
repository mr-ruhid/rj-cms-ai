<?php

namespace RjCommerce\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use RjCommerce\Models\Order;
use RjCommerce\Mail\OrderNotification;

class OrderMailer
{
    public static function send(Order $order, string $eventType = 'created'): void
    {
        $emailsSetting = rj_get_option('commerce_notification_emails');

        if (empty($emailsSetting)) {
            return;
        }

        $emails = array_filter(array_map('trim', preg_split('/[\s,]+/', $emailsSetting)));

        if (empty($emails)) {
            return;
        }

        try {
            Mail::to($emails)->send(new OrderNotification($order, $eventType));
        } catch (\Throwable $e) {
            Log::error('Order Notification Email Failed: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'emails' => $emails
            ]);
        }
    }
}
