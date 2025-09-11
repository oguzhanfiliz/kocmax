<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-posta Doğrulandı | Mutfakyapım Yönetim</title>
    <meta name="robots" content="noindex">
    <style>
        :root { --bg:#0f172a; --card:#111827; --text:#e5e7eb; --muted:#9ca3af; --accent:#22c55e; }
        * { box-sizing: border-box; }
        body { margin:0; padding:0; background: linear-gradient(180deg, #0b1023 0%, #0f172a 100%); font-family: Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji"; color: var(--text); display:flex; align-items:center; justify-content:center; min-height:100vh; }
        .card { background: rgba(17,24,39,0.8); backdrop-filter: blur(10px); border:1px solid rgba(255,255,255,0.06); border-radius: 16px; padding: 28px; width: 100%; max-width: 460px; box-shadow: 0 10px 30px rgba(0,0,0,0.35); text-align: center; }
        .icon { width: 56px; height: 56px; border-radius: 50%; display:inline-flex; align-items:center; justify-content:center; background: rgba(34,197,94,0.12); border:1px solid rgba(34,197,94,0.35); color: var(--accent); margin-bottom: 14px; }
        h1 { font-size: 22px; margin: 6px 0 8px; letter-spacing: 0.2px; }
        p { margin: 0 0 18px; color: var(--muted); font-size: 14px; line-height: 1.5; }
        .btn { display:inline-block; background: var(--accent); color:#052e12; text-decoration:none; padding: 12px 16px; border-radius: 10px; font-weight: 600; transition: transform .08s ease, box-shadow .2s ease; box-shadow: 0 6px 20px rgba(34,197,94,0.28); }
        .btn:hover { transform: translateY(-1px); box-shadow: 0 10px 24px rgba(34,197,94,0.35); }
        .hint { font-size: 12px; color: var(--muted); margin-top: 14px; word-break: break-all; }
        .footer { margin-top: 16px; font-size: 12px; color: #6b7280; }
    </style>
    <script>
        // Otomatik yönlendirme (3 sn)
        document.addEventListener('DOMContentLoaded', function(){
            var url = '{{ $redirectUrl ?? 'https://kocmax.tr' }}';
            setTimeout(function(){ window.location.href = url; }, 3000);
        });
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    </head>
<body>
    <div class="card">
        <div class="icon" aria-hidden="true">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" fill="none"/>
            </svg>
        </div>
        <h1>E-posta başarıyla doğrulandı</h1>
        <p>Hesabınız güvenliğiniz için doğrulandı. Birkaç saniye içinde ana sayfaya yönlendirileceksiniz.</p>
        <a class="btn" href="{{ $redirectUrl ?? 'https://kocmax.tr' }}">Kocmax.tr’ye Dön</a>
        <div class="hint">Yönlendirme olmazsa butona tıklayın: {{ $redirectUrl ?? 'https://kocmax.tr' }}</div>
        <div class="footer">© {{ date('Y') }} Mutfakyapım Yönetim paneli</div>
    </div>
</body>
</html>


