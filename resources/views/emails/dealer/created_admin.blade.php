@php($brand = config('mail.from.name', config('app.name')))
@php($panelUrl = config('app.url'))
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f6f7fb;padding:24px 0;">
  <tr>
    <td align="center">
      <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #e5e7eb;">
        <tr>
          <td style="background:#0ea5e9;color:#ffffff;padding:18px 24px;font-family:Arial,Helvetica,sans-serif;font-size:20px;font-weight:700;">
            {{ $brand }} • Yeni Bayi Başvurusu
          </td>
        </tr>
        <tr>
          <td style="padding:20px 24px 0 24px;font-family:Arial,Helvetica,sans-serif;color:#111827;">
            <p style="margin:0 0 12px 0;font-size:16px;">Merhaba,</p>
            <p style="margin:0 0 16px 0;font-size:15px;line-height:1.6;">Yeni bir bayi başvurusu oluşturuldu. Detaylar aşağıdadır:</p>
          </td>
        </tr>
        <tr>
          <td style="padding:0 24px 0 24px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;font-family:Arial,Helvetica,sans-serif;">
              <tr>
                <td style="background:#f9fafb;padding:10px 12px;color:#6b7280;font-size:12px;letter-spacing:.05em;text-transform:uppercase;" colspan="2">Başvuru Bilgileri</td>
              </tr>
              <tr>
                <td style="padding:10px 12px;color:#6b7280;font-size:14px;width:40%;">Şirket</td>
                <td style="padding:10px 12px;color:#111827;font-size:14px;font-weight:600;">{{ $application->company_name }}</td>
              </tr>
              <tr>
                <td style="padding:10px 12px;color:#6b7280;font-size:14px;">Vergi No</td>
                <td style="padding:10px 12px;color:#111827;font-size:14px;">{{ $application->tax_number }}</td>
              </tr>
              <tr>
                <td style="padding:10px 12px;color:#6b7280;font-size:14px;">Yetkili</td>
                <td style="padding:10px 12px;color:#111827;font-size:14px;">{{ $application->authorized_person_name }}</td>
              </tr>
              <tr>
                <td style="padding:10px 12px;color:#6b7280;font-size:14px;">E‑posta</td>
                <td style="padding:10px 12px;color:#111827;font-size:14px;">{{ $application->email ?? ($user?->email) }}</td>
              </tr>
              <tr>
                <td style="padding:10px 12px;color:#6b7280;font-size:14px;">Telefon</td>
                <td style="padding:10px 12px;color:#111827;font-size:14px;">{{ $application->authorized_person_phone ?? ($user?->phone) }}</td>
              </tr>
              <tr>
                <td style="padding:10px 12px;color:#6b7280;font-size:14px;">Durum</td>
                <td style="padding:10px 12px;color:#111827;font-size:14px;text-transform:capitalize;">{{ $application->status->value }}</td>
              </tr>
              <tr>
                <td style="padding:10px 12px;color:#6b7280;font-size:14px;">Oluşturulma</td>
                <td style="padding:10px 12px;color:#111827;font-size:14px;">{{ $application->created_at }}</td>
              </tr>
              @if($application->trade_registry_document_path)
              <tr>
                <td style="padding:10px 12px;color:#6b7280;font-size:14px;">Ticaret Sicil Belgesi</td>
                <td style="padding:10px 12px;color:#111827;font-size:13px;"><code style="background:#f3f4f6;padding:2px 6px;border-radius:4px;">{{ $application->trade_registry_document_path }}</code></td>
              </tr>
              @endif
              @if($application->tax_plate_document_path)
              <tr>
                <td style="padding:10px 12px;color:#6b7280;font-size:14px;">Vergi Levhası</td>
                <td style="padding:10px 12px;color:#111827;font-size:13px;"><code style="background:#f3f4f6;padding:2px 6px;border-radius:4px;">{{ $application->tax_plate_document_path }}</code></td>
              </tr>
              @endif
              @if($user)
              <tr>
                <td style="padding:10px 12px;color:#6b7280;font-size:14px;">Kullanıcı</td>
                <td style="padding:10px 12px;color:#111827;font-size:14px;">{{ $user->name }} ({{ $user->email }})</td>
              </tr>
              @endif
              <tr>
                <td style="padding:10px 12px;color:#6b7280;font-size:14px;">Başvuru ID</td>
                <td style="padding:10px 12px;color:#111827;font-size:14px;">{{ $application->id }}</td>
              </tr>
              <tr>
                <td style="padding:10px 12px;color:#6b7280;font-size:14px;">Kullanıcı ID</td>
                <td style="padding:10px 12px;color:#111827;font-size:14px;">{{ $application->user_id }}</td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td align="left" style="padding:20px 24px 24px 24px;">
            <a href="{{ $panelUrl }}" style="background:#0ea5e9;color:#ffffff;text-decoration:none;padding:10px 16px;border-radius:8px;font-family:Arial,Helvetica,sans-serif;font-size:14px;display:inline-block;">Yönetim Panelinde Aç</a>
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
