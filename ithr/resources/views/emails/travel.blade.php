<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background-color:#f1f5f9;font-family:Arial,Helvetica,sans-serif;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f5f9;padding:40px 16px;">
  <tr><td align="center">
    <table role="presentation" cellpadding="0" cellspacing="0" style="width:100%;max-width:560px;">

      <!-- App name -->
      <tr>
        <td style="padding:0 0 20px;">
          <span style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.1em;">{{ config('app.name') }}</span>
        </td>
      </tr>

      <!-- Card -->
      <tr>
        <td style="background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">

          <!-- Header -->
          <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
            <tr>
              @php
                $lightColor = match($statusColor) {
                    '#b91c1c' => '#fca5a5',
                    '#15803d' => '#86efac',
                    '#b45309' => '#fcd34d',
                    default   => '#93c5fd'
                };
              @endphp
              <td style="background-color:{{ $statusColor }};padding:28px 32px 24px;">
                <p style="margin:0 0 6px;font-size:11px;font-weight:700;color:{{ $lightColor }};text-transform:uppercase;letter-spacing:0.08em;">My Travel</p>
                <h1 style="margin:0;font-size:21px;font-weight:700;color:#ffffff;line-height:1.3;">{{ $heading }}</h1>
              </td>
            </tr>
          </table>

          <!-- Message -->
          <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td style="padding:28px 32px 20px;">
                <p style="margin:0;font-size:14px;color:#475569;line-height:1.65;">{{ $body }}</p>
              </td>
            </tr>
          </table>

          <!-- Travel details -->
          <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td style="padding:0 32px 20px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;font-size:13px;">
                  <tr style="background-color:#f8fafc;">
                    <td colspan="2" style="padding:10px 16px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.06em;border-bottom:1px solid #e2e8f0;">Travel Details</td>
                  </tr>
                  <tr>
                    <td style="padding:9px 16px;color:#94a3b8;width:36%;border-bottom:1px solid #f1f5f9;">Staff</td>
                    <td style="padding:9px 16px;color:#1e293b;font-weight:600;border-bottom:1px solid #f1f5f9;">{{ $travel->staff?->name ?? '—' }}</td>
                  </tr>
                  <tr style="background-color:#fafbfc;">
                    <td style="padding:9px 16px;color:#94a3b8;border-bottom:1px solid #f1f5f9;">Destination</td>
                    <td style="padding:9px 16px;color:#1e293b;font-weight:600;border-bottom:1px solid #f1f5f9;">{{ $travel->destination }}</td>
                  </tr>
                  <tr>
                    <td style="padding:9px 16px;color:#94a3b8;border-bottom:1px solid #f1f5f9;">Purpose</td>
                    <td style="padding:9px 16px;color:#1e293b;font-weight:600;border-bottom:1px solid #f1f5f9;">{{ $travel->purpose ?: '—' }}</td>
                  </tr>
                  <tr style="background-color:#fafbfc;">
                    <td style="padding:9px 16px;color:#94a3b8;border-bottom:1px solid #f1f5f9;">Departure</td>
                    <td style="padding:9px 16px;color:#1e293b;font-weight:600;border-bottom:1px solid #f1f5f9;">{{\Carbon\Carbon::parse($travel->departure_date)->format('l, d M Y')}}</td>
                  </tr>
                  <tr>
                    <td style="padding:9px 16px;color:#94a3b8;border-bottom:1px solid #f1f5f9;">Return</td>
                    <td style="padding:9px 16px;color:#1e293b;font-weight:600;border-bottom:1px solid #f1f5f9;">{{\Carbon\Carbon::parse($travel->return_date)->format('l, d M Y')}}</td>
                  </tr>
                  <tr style="background-color:#fafbfc;">
                    <td style="padding:9px 16px;color:#94a3b8;">Transport</td>
                    <td style="padding:9px 16px;color:#1e293b;font-weight:600;">{{ $travel->transport ?: '—' }}</td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>

          <!-- CTA Button -->
          <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td style="padding:4px 32px 32px;">
                <a href="{{ config('app.url') }}/travel"
                   style="display:inline-block;background-color:{{ $statusColor }};color:#ffffff;text-decoration:none;padding:11px 22px;border-radius:7px;font-size:14px;font-weight:600;">
                  View Travel Records &rarr;
                </a>
              </td>
            </tr>
          </table>

        </td>
      </tr>

      <!-- Footer -->
      <tr>
        <td style="padding:20px 0 0;">
          <p style="margin:0;font-size:12px;color:#94a3b8;text-align:center;">
            This is an automated notification from {{ config('app.name') }}. Do not reply to this email.
          </p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>

</body>
</html>
