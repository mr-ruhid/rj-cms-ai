<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Order Notification') }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 0; -webkit-font-smoothing: antialiased; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f3f4f6; padding-bottom: 40px; }
        .main { background-color: #ffffff; margin: 0 auto; width: 100%; max-width: 600px; border-spacing: 0; color: #111827; border-radius: 8px; overflow: hidden; margin-top: 40px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .header { background: linear-gradient(135deg, #8b5cf6 0%, #db2777 100%); padding: 30px 40px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 24px; margin: 0; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; }
        .content { padding: 40px; }
        .title { font-size: 20px; font-weight: bold; margin-bottom: 20px; color: #111827; }
        .info-row { width: 100%; margin-bottom: 30px; border-bottom: 1px solid #e5e7eb; padding-bottom: 20px; }
        .info-col { width: 50%; vertical-align: top; font-size: 14px; color: #4b5563; line-height: 1.6; }
        .info-label { font-weight: bold; color: #111827; display: block; margin-bottom: 4px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th { text-align: left; padding: 12px 0; border-bottom: 2px solid #e5e7eb; font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
        .items-table td { padding: 16px 0; border-bottom: 1px solid #e5e7eb; font-size: 14px; color: #111827; }
        .totals { width: 50%; float: right; margin-top: 10px; }
        .totals-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 14px; color: #4b5563; }
        .totals-row.final { border-top: 2px solid #111827; margin-top: 8px; padding-top: 16px; font-weight: bold; font-size: 18px; color: #111827; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #9ca3af; }
        .btn { display: inline-block; background-color: #111827; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: bold; font-size: 14px; margin-top: 20px; }
    </style>
</head>
<body>
    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center">
                <table class="main" width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td class="header">
                            <h1>{{ rj_get_option('commerce_store_name', rj_get_option('site_name', 'RJ Commerce')) }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td class="content">
                            <div class="title">
                                @if($eventType === 'created')
                                    {{ __('New Order Received') }} 🎉
                                @else
                                    {{ __('Order Status Updated') }} 🔄
                                @endif
                            </div>

                            <p style="font-size: 15px; color: #4b5563; line-height: 1.6; margin-bottom: 30px;">
                                @if($eventType === 'created')
                                    {{ __('You have received a new order on your store. Here are the details:') }}
                                @else
                                    {{ __('The status of an order has been updated. Here are the current details:') }}
                                @endif
                            </p>

                            <table class="info-row" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td class="info-col">
                                        <span class="info-label">{{ __('Order Number') }}</span>
                                        #{{ $order->id }}
                                    </td>
                                    <td class="info-col">
                                        <span class="info-label">{{ __('Order Date') }}</span>
                                        {{ $order->created_at->format('M d, Y H:i') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="info-col" style="padding-top: 15px;">
                                        <span class="info-label">{{ __('Current Status') }}</span>
                                        <span style="display: inline-block; padding: 4px 8px; background-color: #f3f4f6; border-radius: 4px; font-weight: bold; text-transform: uppercase; font-size: 12px;">
                                            {{ $order->status_label }}
                                        </span>
                                    </td>
                                    <td class="info-col" style="padding-top: 15px;">
                                        <span class="info-label">{{ __('Payment Method') }}</span>
                                        {{ $order->payment_method ?? __('N/A') }}
                                    </td>
                                </tr>
                            </table>

                            <table class="info-row" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td class="info-col" style="padding-right: 20px;">
                                        <span class="info-label">{{ __('Customer') }}</span>
                                        {{ $order->user?->name ?? __('Guest Customer') }}<br>
                                        {{ $order->user?->email ?? '' }}
                                    </td>
                                    <td class="info-col">
                                        <span class="info-label">{{ __('Shipping Address') }}</span>
                                        @php $shipping = $order->shipping_address ?? []; @endphp
                                        @if(!empty($shipping))
                                            {{ $shipping['first_name'] ?? '' }} {{ $shipping['last_name'] ?? '' }}<br>
                                            {{ $shipping['address_line_1'] ?? '' }}<br>
                                            @if(!empty($shipping['address_line_2'])) {{ $shipping['address_line_2'] }}<br> @endif
                                            {{ $shipping['city'] ?? '' }}, {{ $shipping['state'] ?? '' }} {{ $shipping['postal_code'] ?? '' }}<br>
                                            {{ $shipping['country'] ?? '' }}
                                        @else
                                            {{ __('No address provided.') }}
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <table class="items-table" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <thead>
                                    <tr>
                                        <th>{{ __('Item') }}</th>
                                        <th align="center">{{ __('Qty') }}</th>
                                        <th align="right">{{ __('Price') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        <tr>
                                            <td>
                                                <strong>{{ $item->product_name }}</strong>
                                                @if($item->variation_name)
                                                    <br><span style="font-size: 12px; color: #6b7280;">{{ $item->variation_name }}</span>
                                                @endif
                                            </td>
                                            <td align="center">{{ $item->quantity }}</td>
                                            <td align="right">${{ number_format($item->total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div style="overflow: hidden;">
                                <table class="totals" width="100%" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td style="padding: 8px 0; color: #6b7280;">{{ __('Subtotal') }}</td>
                                        <td align="right" style="padding: 8px 0; font-weight: bold;">${{ number_format($order->subtotal, 2) }}</td>
                                    </tr>
                                    @if($order->shipping_total > 0)
                                        <tr>
                                            <td style="padding: 8px 0; color: #6b7280;">{{ __('Shipping') }}</td>
                                            <td align="right" style="padding: 8px 0; font-weight: bold;">${{ number_format($order->shipping_total, 2) }}</td>
                                        </tr>
                                    @endif
                                    @if($order->tax_total > 0)
                                        <tr>
                                            <td style="padding: 8px 0; color: #6b7280;">{{ __('Tax') }}</td>
                                            <td align="right" style="padding: 8px 0; font-weight: bold;">${{ number_format($order->tax_total, 2) }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td style="border-top: 2px solid #111827; padding: 16px 0 8px 0; font-weight: bold; color: #111827;">{{ __('Total') }}</td>
                                        <td align="right" style="border-top: 2px solid #111827; padding: 16px 0 8px 0; font-weight: bold; font-size: 18px; color: #111827;">
                                            ${{ number_format($order->total, 2) }} <span style="font-size: 12px; font-weight: normal; color: #6b7280;">{{ $order->currency }}</span>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div style="text-align: center; margin-top: 40px;">
                                <a href="{{ route('admin.commerce.orders.show', $order) }}" class="btn">{{ __('View Order Details') }}</a>
                            </div>
                        </td>
                    </tr>
                </table>

                <table width="100%" max-width="600px" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td class="footer">
                            {{ __('This email was sent automatically by') }} <strong>{{ rj_get_option('site_name', 'RJ CMS') }}</strong>.<br>
                            &copy; {{ date('Y') }} {{ __('All rights reserved.') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
