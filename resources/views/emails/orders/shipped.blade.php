<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siparişiniz Kargoya Verildi</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4caf50; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .shipping-info { background: #e8f5e8; padding: 15px; margin: 20px 0; border-left: 4px solid #4caf50; }
        .order-details { background: #f9f9f9; padding: 15px; margin: 20px 0; }
        .footer { background: #f4f4f4; padding: 15px; text-align: center; font-size: 12px; }
        .tracking { background: #fff3cd; padding: 15px; margin: 20px 0; border-left: 4px solid #ffc107; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚚 Siparişiniz Kargoya Verildi!</h1>
            <h2>Sipariş No: #{{ $order->order_number }}</h2>
        </div>

        <div class="content">
            <p>Merhaba {{ $user ? $user->name : 'Değerli Müşterimiz' }},</p>
            
            <div class="shipping-info">
                <h3>📦 Kargo Bilgileri</h3>
                <p>Siparişiniz hazırlandı ve kargo firmasına teslim edildi.</p>
                <p><strong>Kargoya Verilme Tarihi:</strong> {{ now()->format('d.m.Y H:i') }}</p>
            </div>

            @if($trackingNumber)
            <div class="tracking">
                <h3>🔍 Kargo Takip</h3>
                <p><strong>Takip Numarası:</strong> {{ $trackingNumber }}</p>
                <p>Bu takip numarası ile kargo durumunuzu takip edebilirsiniz.</p>
            </div>
            @endif

            <div class="order-details">
                <h3>Sipariş Detayları</h3>
                <p><strong>Sipariş No:</strong> #{{ $order->order_number }}</p>
                <p><strong>Durum:</strong> {{ $order->status->value }}</p>
                <p><strong>Toplam Tutar:</strong> {{ number_format($order->total_amount, 2) }} ₺</p>
                @if($order->shipping_address)
                    <p><strong>Teslimat Adresi:</strong> {{ $order->shipping_address }}</p>
                @endif
                @if($order->shipping_phone)
                    <p><strong>Teslimat Telefonu:</strong> {{ $order->shipping_phone }}</p>
                @endif
            </div>

            <p><strong>Önemli Notlar:</strong></p>
            <ul>
                <li>Paketiniz genellikle 1-3 iş günü içerisinde adresinize teslim edilir</li>
                <li>Teslimat sırasında kimliğinizi hazır bulundurunuz</li>
                <li>Herhangi bir sorun durumunda bizimle iletişime geçiniz</li>
            </ul>
        </div>

        <div class="footer">
            <p>Bu e-posta otomatik olarak gönderilmiştir.</p>
            <p>&copy; {{ date('Y') }} MUTFAK YAPIM - Tüm hakları saklıdır.</p>
        </div>
    </div>
</body>
</html>