<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siparişiniz Oluşturuldu</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f4f4f4; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .order-details { background: #f9f9f9; padding: 15px; margin: 20px 0; }
        .footer { background: #f4f4f4; padding: 15px; text-align: center; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Siparişiniz Oluşturuldu! 🎉</h1>
            <h2>Sipariş No: #{{ $order->order_number }}</h2>
        </div>

        <div class="content">
            <p>Merhaba {{ $user ? $user->name : 'Değerli Müşterimiz' }},</p>
            
            <p>Siparişiniz başarıyla oluşturuldu ve işleme alındı.</p>

            <div class="order-details">
                <h3>Sipariş Detayları</h3>
                <p><strong>Sipariş No:</strong> #{{ $order->order_number }}</p>
                <p><strong>Tarih:</strong> {{ $order->created_at->format('d.m.Y H:i') }}</p>
                <p><strong>Toplam Tutar:</strong> {{ number_format($order->total_amount, 2) }} ₺</p>
                <p><strong>Durum:</strong> {{ $order->status->value }}</p>
                @if($order->shipping_address)
                    <p><strong>Teslimat Adresi:</strong> {{ $order->shipping_address }}</p>
                @endif
            </div>

            @if($items && $items->count() > 0)
            <h3>Sipariş Ürünleri</h3>
            <table>
                <thead>
                    <tr>
                        <th>Ürün</th>
                        <th>Miktar</th>
                        <th>Fiyat</th>
                        <th>Toplam</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->unit_price, 2) }} ₺</td>
                        <td>{{ number_format($item->total_price, 2) }} ₺</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

            <p>Siparişinizin durumunu takip etmek için admin paneline giriş yapabilirsiniz.</p>
        </div>

        <div class="footer">
            <p>Bu e-posta otomatik olarak gönderilmiştir.</p>
            <p>&copy; {{ date('Y') }} MUTFAK YAPIM - Tüm hakları saklıdır.</p>
        </div>
    </div>
</body>
</html>