<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siparişiniz İptal Edildi</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f44336; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .cancelled-info { background: #ffebee; padding: 15px; margin: 20px 0; border-left: 4px solid #f44336; }
        .order-details { background: #f9f9f9; padding: 15px; margin: 20px 0; }
        .footer { background: #f4f4f4; padding: 15px; text-align: center; font-size: 12px; }
        .refund { background: #fff3cd; padding: 15px; margin: 20px 0; border-left: 4px solid #ffc107; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>❌ Siparişiniz İptal Edildi</h1>
            <h2>Sipariş No: #{{ $order->order_number }}</h2>
        </div>

        <div class="content">
            <p>Merhaba {{ $user ? $user->name : 'Değerli Müşterimiz' }},</p>
            
            <div class="cancelled-info">
                <h3>🚫 Sipariş İptali</h3>
                <p>Siparişiniz iptal edilmiştir.</p>
                <p><strong>İptal Tarihi:</strong> {{ now()->format('d.m.Y H:i') }}</p>
            </div>

            <div class="order-details">
                <h3>Sipariş Detayları</h3>
                <p><strong>Sipariş No:</strong> #{{ $order->order_number }}</p>
                <p><strong>Durum:</strong> {{ $order->status->value }}</p>
                <p><strong>Sipariş Tarihi:</strong> {{ $order->created_at->format('d.m.Y H:i') }}</p>
                <p><strong>Toplam Tutar:</strong> {{ number_format($order->total_amount, 2) }} ₺</p>
            </div>

            @if($order->payment_status === 'paid')
            <div class="refund">
                <h3>💰 İade Süreci</h3>
                <p>Ödemeniz alındı ise, iade işlemi otomatik olarak başlatılacaktır.</p>
                <p><strong>İade süresi:</strong> 3-7 iş günü (ödeme yönteminize göre değişir)</p>
                <ul>
                    <li>Kredi kartı ile ödeme: 3-5 iş günü</li>
                    <li>Banka havalesi: 5-7 iş günü</li>
                    <li>EFT: 3-5 iş günü</li>
                </ul>
            </div>
            @endif

            <p><strong>İptal Nedenleri:</strong></p>
            <ul>
                <li>Stok yetersizliği</li>
                <li>Teknik problemler</li>
                <li>Müşteri talebi</li>
                <li>Ödeme problemi</li>
                <li>Diğer operasyonel nedenler</li>
            </ul>

            <p><strong>Ne yapabilirsiniz:</strong></p>
            <ul>
                <li>Benzer ürünleri tekrar sipariş verebilirsiniz</li>
                <li>İade süreci hakkında sorularınız için bizimle iletişime geçebilirsiniz</li>
                <li>Destek ekibimizden yardım alabilirsiniz</li>
            </ul>

            <p>Bu durum için özür diler, anlayışınız için teşekkür ederiz.</p>
        </div>

        <div class="footer">
            <p>Bu e-posta otomatik olarak gönderilmiştir.</p>
            <p>&copy; {{ date('Y') }} MUTFAK YAPIM - Tüm hakları saklıdır.</p>
        </div>
    </div>
</body>
</html>