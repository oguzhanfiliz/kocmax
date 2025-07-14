<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sunucu Hatası - 500</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #333;
        }
        .error-container {
            background: white;
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            text-align: center;
            max-width: 500px;
            margin: 20px;
        }
        .error-code {
            font-size: 6rem;
            font-weight: bold;
            color: #e74c3c;
            margin: 0;
            line-height: 1;
        }
        .error-title {
            font-size: 2rem;
            margin: 1rem 0;
            color: #2c3e50;
        }
        .error-message {
            font-size: 1.1rem;
            color: #7f8c8d;
            margin: 1.5rem 0;
            line-height: 1.6;
        }
        .error-actions {
            margin-top: 2rem;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            margin: 0 10px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: background 0.3s ease;
        }
        .btn:hover {
            background: #2980b9;
        }
        .btn-secondary {
            background: #95a5a6;
        }
        .btn-secondary:hover {
            background: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">500</div>
        <h1 class="error-title">Sunucu Hatası</h1>
        <p class="error-message">
            @if(isset($message))
                {{ $message }}
            @else
                Üzgünüz, beklenmedik bir hata oluştu. Lütfen daha sonra tekrar deneyin.
            @endif
        </p>
        <div class="error-actions">
            <a href="javascript:history.back()" class="btn btn-secondary">← Geri Dön</a>
            <a href="{{ url('/') }}" class="btn">🏠 Ana Sayfa</a>
        </div>
    </div>
</body>
</html> 