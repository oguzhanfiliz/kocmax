<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesajınız Alındı - MUTFAK YAPIM</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4caf50; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .success-message { background: #e8f5e8; padding: 15px; margin: 20px 0; border-left: 4px solid #4caf50; }
        .message-summary { background: #f8f9fa; padding: 15px; margin: 20px 0; border-left: 4px solid #007bff; }
        .footer { background: #f4f4f4; padding: 15px; text-align: center; font-size: 12px; }
        .contact-info { background: #e3f2fd; padding: 15px; margin: 20px 0; }
        .what-happens-next { background: #fff8e1; padding: 15px; margin: 20px 0; border-left: 4px solid #ffc107; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Mesajınız Başarıyla Alındı!</h1>
            <p>MUTFAK YAPIM İletişim Sistemi</p>
        </div>

        <div class="content">
            <p>Merhaba <strong>{{ $contact->name }}</strong>,</p>

            <div class="success-message">
                <h3>🎉 Teşekkürler!</h3>
                <p>Mesajınız başarıyla alındı ve kayıtlarımıza eklendi.</p>
                <p><strong>Mesaj No:</strong> #{{ $contact->id }}</p>
                <p><strong>Alınma Tarihi:</strong> {{ $contact->created_at->format('d.m.Y H:i') }}</p>
            </div>

            <div class="message-summary">
                <h3>📋 Gönderdiğiniz Mesajın Özeti</h3>
                <p><strong>Konu:</strong> {{ $contact->subject }}</p>
                <p><strong>E-posta:</strong> {{ $contact->email }}</p>
                @if($contact->phone)
                    <p><strong>Telefon:</strong> {{ $contact->phone }}</p>
                @endif
                @if($contact->company)
                    <p><strong>Firma:</strong> {{ $contact->company }}</p>
                @endif
            </div>

            <div class="what-happens-next">
                <h3>⏰ Bundan Sonra Ne Olacak?</h3>
                <ul>
                    <li><strong>İnceleme:</strong> Mesajınız uzman ekibimiz tarafından incelenecek</li>
                    <li><strong>Değerlendirme:</strong> İhtiyaçlarınız analiz edilecek</li>
                    <li><strong>Yanıt:</strong> Size özel çözüm önerileri hazırlanacak</li>
                    <li><strong>İletişim:</strong> En kısa sürede (genellikle 24 saat içinde) size dönüş yapılacak</li>
                </ul>
            </div>

            <div class="contact-info">
                <h3>📞 Acil Durumlarda İletişim</h3>
                <p>Konunuz acil ise aşağıdaki kanallardan da bize ulaşabilirsiniz:</p>
                <ul>
                    <li><strong>Telefon:</strong> +90 (XXX) XXX XX XX</li>
                    <li><strong>WhatsApp:</strong> +90 (XXX) XXX XX XX</li>
                    <li><strong>E-posta:</strong> info@mutfakyapim.net</li>
                    <li><strong>Çalışma Saatleri:</strong> Pazartesi-Cumartesi 09:00-18:00</li>
                </ul>
            </div>

            <p><strong>🏢 MUTFAK YAPIM Hakkında</strong></p>
            <p>Profesyonel mutfak çözümleri ve iş güvenliği alanında uzman ekibimizle, sizlere en kaliteli hizmeti sunmak için çalışıyoruz. Güveniniz bizim için çok değerli!</p>

            <div style="text-align: center; margin: 30px 0;">
                <p><strong>Bizi tercih ettiğiniz için teşekkürler! 🙏</strong></p>
            </div>
        </div>

        <div class="footer">
            <p>Bu e-posta otomatik olarak gönderilmiştir.</p>
            <p>&copy; {{ date('Y') }} MUTFAK YAPIM - Tüm hakları saklıdır.</p>
            <p><small>Bu mesajı yanıtlamayınız. Cevap vermek için yukarıdaki iletişim bilgilerini kullanınız.</small></p>
        </div>
    </div>
</body>
</html>