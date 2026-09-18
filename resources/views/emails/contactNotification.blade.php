@php
    $contact = $contact ?? null;
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Liên hệ mới</title>
</head>
<body style="margin:0;padding:0;background:#f5f7fb;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f5f7fb;padding:20px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="760" cellpadding="0" cellspacing="0" border="0" style="max-width:760px;background:#ffffff;border:1px solid #e5e7eb;border-radius:8px;">
                    <tr>
                        <td style="padding:20px 24px;border-bottom:1px solid #e5e7eb;">
                            <h2 style="margin:0;font-size:24px;color:#111827;">Bạn có liên hệ mới</h2>
                            <p style="margin:8px 0 0;font-size:14px;color:#6b7280;">
                                Mã tin: <strong>#{{ $contact->id ?? 'N/A' }}</strong>
                                @if(!empty($contact->created_at))
                                    — {{ $contact->created_at }}
                                @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 24px;">
                            <h3 style="margin:0 0 12px;font-size:18px;color:#111827;">Thông tin khách</h3>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="font-size:14px;line-height:1.7;">
                                <tr>
                                    <td style="width:180px;color:#6b7280;">Họ và tên:</td>
                                    <td><strong>{{ $contact->name ?? 'N/A' }}</strong></td>
                                </tr>
                                <tr>
                                    <td style="color:#6b7280;">Email:</td>
                                    <td>{{ $contact->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td style="color:#6b7280;">Số điện thoại:</td>
                                    <td>{{ $contact->phone ?: 'Không có' }}</td>
                                </tr>
                                @if(!empty($contact->service_name))
                                <tr>
                                    <td style="color:#6b7280;">Dịch vụ:</td>
                                    <td>{{ $contact->service_name }}</td>
                                </tr>
                                @endif
                                @if(!empty($contact->service_slug))
                                <tr>
                                    <td style="color:#6b7280;">Slug dịch vụ:</td>
                                    <td>{{ $contact->service_slug }}</td>
                                </tr>
                                @endif
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 24px 20px;">
                            <h3 style="margin:0 0 12px;font-size:18px;color:#111827;">Nội dung</h3>
                            <div style="font-size:14px;line-height:1.7;white-space:pre-wrap;background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;padding:14px;">{{ $contact->mess ?? 'Không có' }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:14px 24px;border-top:1px solid #e5e7eb;font-size:12px;color:#6b7280;text-align:center;">
                            Email này được gửi tự động từ form liên hệ trên website.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
