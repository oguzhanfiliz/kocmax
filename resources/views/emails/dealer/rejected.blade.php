@php($brand = config('mail.from.name', config('app.name')))
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f6f7fb;padding:24px 0;">
  <tr>
    <td align="center">
      <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #e5e7eb;">
        <tr>
          <td style="background:#ef4444;color:#ffffff;padding:18px 24px;font-family:Arial,Helvetica,sans-serif;font-size:20px;font-weight:700;">
            {{ $brand }}
          </td>
        </tr>
        <tr>
          <td style="padding:24px 24px 8px 24px;font-family:Arial,Helvetica,sans-serif;color:#111827;">
            <p style="margin:0 0 12px 0;font-size:16px;">Merhaba <strong>{{ $user?->name ?? 'Kullanıcı' }}</strong>,</p>
            <p style="margin:0 0 8px 0;font-size:15px;line-height:1.6;">
              <strong>{{ $application->company_name }}</strong> için yaptığınız bayi başvurusu <span style="color:#b91c1c;">reddedildi</span>.
            </p>
            <p style="margin:8px 0 0 0;font-size:15px;line-height:1.6;color:#374151;">Detaylı bilgi için bizimle iletişime geçebilirsiniz.</p>
          </td>
        </tr>
        <tr>
          <td style="background:#f3f4f6;padding:16px 24px;font-family:Arial,Helvetica,sans-serif;color:#6b7280;font-size:12px;border-top:1px solid #e5e7eb;">
            Saygılarımızla,<br>
            <strong style="color:#111827;">{{ $brand }}</strong>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
