<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">

</head>
<body style="margin:0;padding:0;background:#f4f5f7;">
    <div style="margin:0;padding:0;background:#f4f5f7;font-family:Arial,Helvetica,sans-serif;">
      <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f5f7;padding:24px 0;">
        <tr><td align="center">
          <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
            <tr>
              <td align="center" style="background:#ffffff;padding:28px 20px 16px;border-bottom:3px solid #DC746C;">
                @if($hasLogo)
                    <img src="{{ $message->embed(public_path('frontend/assets/img/logo/tata-trust-logo.webp')) }}" alt="Small Animal Hospital Mumbai" width="180" style="width:180px;max-width:55%;height:auto;display:block;margin:0 auto;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;">
                @else
                    <h2 style="margin:0;color:#DC746C;font-size:20px;">Small Animal Hospital Mumbai</h2>
                @endif
              </td>
            </tr>
            <tr>
              <td style="padding:30px 36px 10px;color:#333333;">
                <h2 style="margin:0 0 8px;color:#DC746C;font-size:22px;">{{ $heading }}</h2>
                <p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#555;">{!! $intro !!}</p>
                <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
                  <tr>
                    <td style="padding:10px 0;border-bottom:1px solid #eee;color:#888;width:42%;">Name</td>
                    <td style="padding:10px 0;border-bottom:1px solid #eee;color:#222;font-weight:bold;">{{ $enquiry->full_name }}</td>
                  </tr>
                  <tr>
                    <td style="padding:10px 0;border-bottom:1px solid #eee;color:#888;">Email</td>
                    <td style="padding:10px 0;border-bottom:1px solid #eee;color:#222;font-weight:bold;">{{ $enquiry->email }}</td>
                  </tr>
                  <tr>
                    <td style="padding:10px 0;border-bottom:1px solid #eee;color:#888;">Phone</td>
                    <td style="padding:10px 0;border-bottom:1px solid #eee;color:#222;font-weight:bold;">{{ $enquiry->phone }}</td>
                  </tr>
                  <tr>
                    <td style="padding:10px 0;{{ !empty($enquiry->message) ? 'border-bottom:1px solid #eee;' : '' }}color:#888;">Subject</td>
                    <td style="padding:10px 0;{{ !empty($enquiry->message) ? 'border-bottom:1px solid #eee;' : '' }}color:#222;font-weight:bold;">{{ $enquiry->subject }}</td>
                  </tr>
                  @if(!empty($enquiry->message))
                    <tr>
                      <td style="padding:10px 0;border-bottom:1px solid #eee;color:#888;vertical-align:top;">Message</td>
                      <td style="padding:10px 0;border-bottom:1px solid #eee;color:#222;">{!! nl2br(e($enquiry->message)) !!}</td>
                    </tr>
                  @endif
                </table>
                @if(!empty($note))
                    <div style="margin-top:22px;padding:14px 16px;background:#fbeeed;border-left:4px solid #DC746C;border-radius:6px;font-size:13.5px;line-height:1.6;color:#7a5450;">
                        {!! $note !!}
                    </div>
                @endif
              </td>
            </tr>
            <tr>
              <td style="padding:22px 36px 30px;color:#999;font-size:12px;line-height:1.6;border-top:1px solid #f0f0f0;">
                Tata Trusts Small Animal Hospital, G. Babu Sakpal Marg, Saat Rasta, Mahalaxmi, Mumbai 400011<br>
                Phone: 022-6538-3538 &nbsp;|&nbsp; frontoffice@sahmumbai.com<br><br>
                <span style="color:#bbb;">This is an automated message. Please do not reply directly to this email.</span>
              </td>
            </tr>
          </table>
        </td></tr>
      </table>
    </div>
</body>
</html>
