<p>Merhaba {{ $user?->name ?? 'Kullanıcı' }},</p>
<p>{{ $application->company_name }} için yaptığınız bayi başvurusu <strong>onaylandı</strong>.</p>
<p>Bayi kodunuz: <strong>{{ $user?->dealer_code }}</strong></p>
<p>Artık bayi fiyatları ve sipariş ayrıcalıklarından yararlanabilirsiniz.</p>
<p>Saygılarımızla,<br/>{{ config('app.name') }}</p>
