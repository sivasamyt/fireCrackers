<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>New order {{ $order->order_number }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #222; line-height: 1.5;">
    <h1 style="font-size: 20px;">New order {{ $order->order_number }}</h1>

    <h2 style="font-size: 16px; margin-bottom: 8px;">Customer</h2>
    <p style="margin: 0 0 4px;">Name: {{ $order->customerName() }}</p>
    <p style="margin: 0 0 4px;">Mobile: {{ $order->customerPhone() ?? '—' }}</p>
    <p style="margin: 0 0 16px;">Address: {{ $order->fullAddress() }}</p>

    <h2 style="font-size: 16px; margin-bottom: 8px;">Payment</h2>
    <p style="margin: 0 0 4px;">Method: {{ strtoupper($order->payment_method) }}</p>
    <p style="margin: 0 0 16px;">Status: {{ $order->payment_status }}</p>

    <h2 style="font-size: 16px; margin-bottom: 8px;">Products</h2>
    <table cellpadding="8" cellspacing="0" border="1" style="border-collapse: collapse; width: 100%; max-width: 640px;">
        <thead>
            <tr>
                <th align="left">Item</th>
                <th align="right">Qty</th>
                <th align="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td align="right">{{ $item->quantity }}</td>
                    <td align="right">₹{{ number_format($item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top: 16px; font-size: 16px; font-weight: bold;">
        Grand total: ₹{{ number_format($order->grand_total, 2) }}
    </p>
</body>
</html>
