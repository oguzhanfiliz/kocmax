<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siparişiniz Teslim Edildi</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4caf50; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .delivered-info { background: #e8f5e8; padding: 15px; margin: 20px 0; border-left: 4px solid #4caf50; }
        .order-details { background: #f9f9f9; padding: 15px; margin: 20px 0; }
        .footer { background: #f4f4f4; padding: 15px; text-align: center; font-size: 12px; }
        .feedback { background: #e3f2fd; padding: 15px; margin: 20px 0; border-left: 4px solid #2196f3; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Siparişiniz Teslim Edildi!</h1>
            <h2>Sipariş No: #{{ $order->order_number }}</h2>
        </div>

        <div class="content">
            <p>Merhaba {{ $user ? $user->name : 'Değerli Müşterimiz' }},</p>
            
            <div class="delivered-info">
                <h3>✅ Teslimat Tamamlandı</h3>
                <p>Siparişiniz başarıyla teslim edildi!</p>
                <p><strong>Teslimat Tarihi:</strong> {{ now()->format('d.m.Y H:i') }}</p>
            </div>

            <div class="order-details">
                <h3>Sipariş Detayları</h3>
                <p><strong>Sipariş No:</strong> #{{ $order->order_number }}</p>
                <p><strong>Durum:</strong> {{ $order->status->value }}</p>
                <p><strong>Toplam Tutar:</strong> {{ number_format($order->total_amount, 2) }} ₺</p>
                <p><strong>Sipariş Tarihi:</strong> {{ $order->created_at->format('d.m.Y H:i') }}</p>
            </div>

            <div class="feedback">
                <h3>💬 Görüşünüz Bizim İçin Önemli</h3>
                <p>Ürünlerimiz ve hizmetimiz hakkında görüşlerinizi bizimle paylaşır mısınız?</p>
                <p>Deneyiminizi değerlendirmeniz, gelecekteki müşterilerimize yardımcı olur.</p>
            </div>

            <p><strong>Önemli Bilgiler:</strong></p>
            <ul>
                <li>Ürünü teslim aldıktan sonra kontrol ediniz</li>
                <li>Herhangi bir sorun varsa en kısa sürede bizimle iletişime geçiniz</li>
                <li>Garanti kapsamında olan ürünler için faturanızı saklayınız</li>
                <li>İade ve değişim için 14 gün süreniz bulunmaktadır</li>
            </ul>

            <p>MUTFAK YAPIM'i tercih ettiğiniz için teşekkür ederiz! 🙏</p>
        </div>

        <div class="footer">
            <p>Bu e-posta otomatik olarak gönderilmiştir.</p>
            <p>&copy; {{ date('Y') }} MUTFAK YAPIM - Tüm hakları saklıdır.</p>
        </div>
    </div>
</body>
</html>