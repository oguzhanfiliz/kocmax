<p>Merhaba {{ $user?->name ?? 'Kullanıcı' }},</p>
<p>{{ $application->company_name }} için yaptığınız bayi başvurusu <strong>reddedildi</strong>.</p>
<p>Detaylı bilgi için bizimle iletişime geçebilirsiniz.</p>
<p>Saygılarımızla,<br/>{{ config('app.name') }}</p>
