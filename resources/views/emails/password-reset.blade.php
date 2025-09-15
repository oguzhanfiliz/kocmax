<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kocmax - Şifre Sıfırlama</title>
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
        <h1>Kocmax</h1>
        <p>Şifre Sıfırlama Talebi</p>
    </div>
    
    <div class="content">
        <h2>Merhaba {{ $user->name }},</h2>
        
        <p>Hesabınız için bir şifre sıfırlama talebi aldık. Eğer bu talebi siz yapmadıysanız, bu e-postayı görmezden gelebilirsiniz.</p>
        
        <p>Şifrenizi sıfırlamak için aşağıdaki butona tıklayın:</p>
        
        <div style="text-align: center;">
            <a href="{{ $resetUrl }}" class="button">Şifre Sıfırla</a>
        </div>
        
        <p>Eğer buton çalışmıyorsa, aşağıdaki linki tarayıcınıza kopyalayabilirsiniz:</p>
        <p style="word-break: break-all; background-color: #f1f5f9; padding: 10px; border-radius: 4px;">
            {{ $resetUrl }}
        </p>
        
        <p><strong>Önemli Notlar:</strong></p>
        <ul>
            <li>Bu link 60 dakika geçerlidir</li>
            <li>E-posta doğrulaması tamamlanmadan giriş yapamazsınız</li>
            <li>Herhangi bir sorun yaşarsanız destek ekibimizle iletişime geçin</li>
        </ul>
        
        <p>Teşekkürler,<br>
        <strong>Kocmax Ekibi</strong></p>
    </div>
    
    <div class="footer">
        <p>Bu e-posta otomatik olarak gönderilmiştir. Lütfen yanıtlamayınız.</p>
        <p>&copy; {{ date('Y') }} Kocmax. Tüm hakları saklıdır.</p>
    </div>
</body>
</html>
