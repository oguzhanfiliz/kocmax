<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('PROJECT_NAME', config('app.name')) }} - E-posta Doğrulama</title>
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
            background-color: #2563eb;
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
            background-color: #2563eb;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ env('PROJECT_NAME', config('app.name')) }}</h1>
        <p>E-posta Adresinizi Doğrulayın</p>
    </div>
    
    <div class="content">
        <h2>Merhaba {{ $user->name }},</h2>
        
        <p>{{ env('PROJECT_NAME', config('app.name')) }} platformumuza hoş geldiniz! Hesabınızı aktifleştirmek için e-posta adresinizi doğrulamanız gerekmektedir.</p>
        
        <p>E-posta adresinizi doğrulamak için aşağıdaki butona tıklayın:</p>
        
        <div style="text-align: center;">
            <a href="{{ $verificationUrl }}" class="button">E-posta Adresimi Doğrula</a>
        </div>
        
        <p>Eğer buton çalışmıyorsa, aşağıdaki linki tarayıcınıza kopyalayabilirsiniz:</p>
        <p style="word-break: break-all; background-color: #f1f5f9; padding: 10px; border-radius: 4px;">
            {{ $verificationUrl }}
        </p>
        
        <p><strong>Önemli Notlar:</strong></p>
        <ul>
            <li>Bu link 24 saat geçerlidir</li>
            <li>E-posta doğrulaması tamamlanmadan giriş yapamazsınız</li>
            <li>Herhangi bir sorun yaşarsanız destek ekibimizle iletişime geçin</li>
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
