<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Durumu Güncellendi</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f4f4f4; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .status-update { background: #e7f3ff; padding: 15px; margin: 20px 0; border-left: 4px solid #2196f3; }
        .order-details { background: #f9f9f9; padding: 15px; margin: 20px 0; }
        .footer { background: #f4f4f4; padding: 15px; text-align: center; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Sipariş Durumu Güncellendi 📋</h1>
            <h2>Sipariş No: #{{ $order->order_number }}</h2>
        </div>

        <div class="content">
            <p>Merhaba {{ $user ? $user->name : 'Değerli Müşterimiz' }},</p>
            
            <div class="status-update">
                <h3>📍 Durum Güncellemesi</h3>
                <p><strong>{{ $statusMessage }}</strong></p>
            </div>

            <div class="order-details">
                <h3>Sipariş Detayları</h3>
                <p><strong>Sipariş No:</strong> #{{ $order->order_number }}</p>
                <p><strong>Yeni Durum:</strong> {{ \App\Enums\OrderStatus::from((string) $order->status)->getLabel() }}</p>
                <p><strong>Güncelleme Tarihi:</strong> {{ now()->format('d.m.Y H:i') }}</p>
                <p><strong>Toplam Tutar:</strong> {{ number_format($order->total_amount, 2) }} ₺</p>
                
                @if($order->tracking_number)
                    <p><strong>Kargo Takip No:</strong> {{ $order->tracking_number }}</p>
                @endif
            </div>

            <p>Siparişinizin detaylarını görmek için admin paneline giriş yapabilirsiniz.</p>
        </div>

        <div class="footer">
            <p>Bu e-posta otomatik olarak gönderilmiştir.</p>
            <p>&copy; {{ date('Y') }} MUTFAK YAPIM - Tüm hakları saklıdır.</p>
        </div>
    </div>
</body>
</html>
