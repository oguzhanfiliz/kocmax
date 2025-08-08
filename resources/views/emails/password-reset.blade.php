<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Şifre Sıfırlama</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #dc2626;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f8fafc;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .button {
            display: inline-block;
            background-color: #dc2626;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
        .warning {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ env('PROJECT_NAME', config('app.name')) }}</h1>
        <p>Şifre Sıfırlama Talebi</p>
    </div>
    
    <div class="content">
        <h2>Merhaba {{ $user->name }},</h2>
        
        <p>Hesabınız için bir şifre sıfırlama talebi aldık. Eğer bu talebi siz yapmadıysanız, bu e-postayı görmezden gelebilirsiniz.</p>
        
        <p>Şifrenizi sıfırlamak için aşağıdaki butona tıklayın:</p>
        
        <div style="text-align: center;">
            <a href="{{ $resetUrl }}" class="button">Şifremi Sıfırla</a>
        </div>
        
        <p>Eğer buton çalışmıyorsa, aşağıdaki linki tarayıcınıza kopyalayabilirsiniz:</p>
        <p style="word-break: break-all; background-color: #f1f5f9; padding: 10px; border-radius: 4px;">
            {{ $resetUrl }}
        </p>
        
        <div class="warning">
            <p><strong>Güvenlik Uyarısı:</strong></p>
            <ul>
                <li>Bu link sadece 60 dakika geçerlidir</li>
                <li>Şifrenizi kimseyle paylaşmayın</li>
                <li>Şüpheli aktivite fark ederseniz hemen destek ekibimizle iletişime geçin</li>
            </ul>
        </div>
        
        <p><strong>Önemli Notlar:</strong></p>
        <ul>
            <li>Şifrenizi güçlü tutun (en az 8 karakter, büyük/küçük harf, rakam)</li>
            <li>Farklı hesaplarınız için aynı şifreyi kullanmayın</li>
            <li>Şifrenizi düzenli olarak değiştirin</li>
        </ul>
        
        <p>Teşekkürler,<br>
        <strong>{{ env('PROJECT_NAME', config('app.name')) }} Ekibi</strong></p>
    </div>
    
    <div class="footer">
        <p>Bu e-posta otomatik olarak gönderilmiştir. Lütfen yanıtlamayınız.</p>
        <p>&copy; {{ date('Y') }} {{ env('PROJECT_COPYRIGHT', config('app.name')) }}. Tüm hakları saklıdır.</p>
        <p>Destek: {{ env('PROJECT_SUPPORT_EMAIL') }} | {{ env('PROJECT_SUPPORT_PHONE') }}</p>
    </div>
</body>
</html>
