<?php

namespace RjCommerce\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use RjCommerce\Models\Order;

class OrderNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public string $eventType;

    public function __construct(Order $order, string $eventType = 'created')
    {
        $this->order = $order;
        $this->eventType = $eventType;
    }

    public function envelope(): Envelope
    {
        $subject = $this->eventType === 'created'
            ? __('New Order Received: #:id', ['id' => $this->order->id])
            : __('Order Status Updated: #:id', ['id' => $this->order->id]);

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'plugin-rj-commerce::emails.order-notification',
        );
    }
}
