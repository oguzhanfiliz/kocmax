<!doctype html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş {{ $order->order_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .header { display:flex; justify-content:space-between; margin-bottom: 16px; }
        .title { font-size: 18px; font-weight: bold; }
        .muted { color: #555; }
        .box { border:1px solid #ddd; padding:10px; border-radius:4px; }
        table { width:100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border:1px solid #ddd; padding:6px; }
        th { background:#f5f5f5; text-align:left; }
        .totals { margin-top: 12px; width: 40%; margin-left: auto; }
        .totals td { border:none; padding:4px 0; }
        .totals .label { color:#555; }
        .totals .value { text-align: right; font-weight: bold; }
    </style>
    @php
        $fmt = fn($n) => number_format((float)$n, 2, ',', '.') . ' ₺';
    @endphp
    </head>
<body>
    <div class="header">
        <div>
            <div class="title">Sipariş Fişi</div>
            <div class="muted">No: {{ $order->order_number }}</div>
            <div class="muted">Tarih: {{ $order->created_at?->format('d.m.Y H:i') }}</div>
        </div>
        <div class="box">
            <div><strong>Müşteri:</strong> {{ $order->billing_name ?? $order->user?->name ?? 'Müşteri' }}</div>
            <div><strong>E-posta:</strong> {{ $order->billing_email ?? $order->user?->email }}</div>
            <div><strong>Telefon:</strong> {{ $order->billing_phone ?? $order->user?->phone }}</div>
        </div>
    </div>

    <div class="box">
        <strong>Teslimat Adresi</strong>
        <div>{{ $order->shipping_name }}</div>
        <div>{{ $order->shipping_address }}</div>
        <div>{{ $order->shipping_city }} / {{ $order->shipping_country }}</div>
    </div>

    <table>
        <thead>
        <tr>
            <th>Ürün</th>
            <th>Miktar</th>
            <th>Birim Fiyat</th>
            <th>Ara Toplam</th>
        </tr>
        </thead>
        <tbody>
        @foreach($order->items as $item)
            <tr>
                <td>
                    {{ $item->product_name }}
                    @if(!empty($item->product_attributes))
                        <div class="muted">
                            @foreach(($item->product_attributes ?? []) as $k => $v)
                                {{ ucfirst($k) }}: {{ $v }}@if(!$loop->last), @endif
                            @endforeach
                        </div>
                    @endif
                </td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $fmt($item->price) }}</td>
                <td>{{ $fmt($item->quantity * $item->price) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="label">Ara Toplam</td>
            <td class="value">{{ $fmt($order->subtotal) }}</td>
        </tr>
        <tr>
            <td class="label">İndirim</td>
            <td class="value">{{ $fmt($order->discount_amount) }}</td>
        </tr>
        <tr>
            <td class="label">KDV</td>
            <td class="value">{{ $fmt($order->tax_amount) }}</td>
        </tr>
        <tr>
            <td class="label">Kargo</td>
            <td class="value">{{ $fmt($order->shipping_amount) }}</td>
        </tr>
        <tr>
            <td class="label">Toplam</td>
            <td class="value">{{ $fmt($order->total_amount) }}</td>
        </tr>
    </table>
</body>
</html>

