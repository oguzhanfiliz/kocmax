<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni İletişim Mesajı</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2196f3; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .message-info { background: #f8f9fa; padding: 15px; margin: 20px 0; border-left: 4px solid #2196f3; }
        .contact-details { background: #e8f4fd; padding: 15px; margin: 20px 0; }
        .message-content { background: #fff; padding: 20px; border: 1px solid #dee2e6; margin: 20px 0; }
        .footer { background: #f4f4f4; padding: 15px; text-align: center; font-size: 12px; }
        .priority { color: #dc3545; font-weight: bold; }
        .admin-actions { background: #d4edda; padding: 15px; margin: 20px 0; border-left: 4px solid #28a745; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📧 Yeni İletişim Mesajı</h1>
            <p>MUTFAK YAPIM - Admin Bildirimi</p>
        </div>

        <div class="content">
            <div class="message-info">
                <h3>📋 Mesaj Bilgileri</h3>
                <p><strong>Mesaj ID:</strong> #{{ $contact->id }}</p>
                <p><strong>Tarih:</strong> {{ $contact->created_at->format('d.m.Y H:i:s') }}</p>
                <p><strong>Durum:</strong> <span class="priority">YENİ</span></p>
                <p><strong>Konu:</strong> {{ $contact->subject }}</p>
            </div>

            <div class="contact-details">
                <h3>👤 Gönderen Bilgileri</h3>
                <p><strong>Ad Soyad:</strong> {{ $contact->name }}</p>
                <p><strong>E-posta:</strong> <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></p>
                @if($contact->phone)
                    <p><strong>Telefon:</strong> <a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a></p>
                @endif
                @if($contact->company)
                    <p><strong>Firma:</strong> {{ $contact->company }}</p>
                @endif
                <p><strong>IP Adresi:</strong> {{ $contact->ip_address }}</p>
            </div>

            <div class="message-content">
                <h3>💬 Mesaj İçeriği</h3>
                <p style="white-space: pre-wrap; font-size: 14px; line-height: 1.6;">{{ $contact->message }}</p>
            </div>

            <div class="admin-actions">
                <h3>⚡ Hızlı İşlemler</h3>
                <ul>
                    <li><strong>Yanıtla:</strong> <a href="mailto:{{ $contact->email }}?subject=Re: {{ $contact->subject }}">{{ $contact->email }}</a></li>
                    <li><strong>Telefon Et:</strong> 
                        @if($contact->phone)
                            <a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a>
                        @else
                            Telefon numarası belirtilmemiş
                        @endif
                    </li>
                    <li><strong>Admin Panel:</strong> İletişim mesajlarını yönetmek için admin paneline giriş yapın</li>
                </ul>
            </div>

            <div style="background: #fff3cd; padding: 15px; margin: 20px 0; border-left: 4px solid #ffc107;">
                <h4>⚠️ Önemli Hatırlatma</h4>
                <p>Bu mesaj <strong>{{ $contact->created_at->diffForHumans() }}</strong> gönderildi.</p>
                <p>Müşteri memnuniyeti için en geç 24 saat içerisinde yanıtlanması önerilir.</p>
            </div>
        </div>

        <div class="footer">
            <p>Bu e-posta otomatik olarak gönderilmiştir.</p>
            <p>&copy; {{ date('Y') }} MUTFAK YAPIM - Tüm hakları saklıdır.</p>
            <p><small>Mesaj #{{ $contact->id }} - {{ $contact->created_at->format('Y-m-d H:i:s') }}</small></p>
        </div>
    </div>
</body>
</html>