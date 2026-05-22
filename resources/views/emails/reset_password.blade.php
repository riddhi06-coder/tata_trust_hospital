<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f9; font-family: Arial, Helvetica, sans-serif; color:#333;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f4f6f9; padding:30px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.05);">
                    <tr>
                        <td align="center" style="padding:30px 20px 10px; background-color:#ffffff; border-bottom:1px solid #eee;">
                            <a href="{{ url('/') }}" style="text-decoration:none;">
                                <img src="cid:logo@tata-trust" alt="{{ $appName }}" style="max-width:230px; height:auto; display:block;">
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:30px 40px 10px;">
                            <h2 style="margin:0 0 16px; font-size:22px; color:#222;">Reset Your Password</h2>

                            <p style="margin:0 0 16px; font-size:15px; line-height:1.6;">
                                @if($userName) Hello {{ $userName }}, @else Hello, @endif
                            </p>

                            <p style="margin:0 0 16px; font-size:15px; line-height:1.6;">
                                You are receiving this email because we received a password reset request for your account.
                                Click the button below to choose a new password.
                            </p>

                            <p style="margin:30px 0; text-align:center;">
                                <a href="{{ $url }}"
                                   style="background-color:#7366ff; color:#ffffff; text-decoration:none; padding:12px 28px; border-radius:6px; font-weight:600; display:inline-block;">
                                    Reset Password
                                </a>
                            </p>

                            <p style="margin:0 0 16px; font-size:14px; line-height:1.6; color:#555;">
                                This password reset link will expire in {{ $expireMinutes }} minutes.
                                If you did not request a password reset, no further action is required and you can safely ignore this email.
                            </p>

                            <p style="margin:24px 0 8px; font-size:13px; color:#888;">
                                If the button above doesn't work, copy and paste this link into your browser:
                            </p>
                            <p style="margin:0 0 20px; font-size:12px; color:#7366ff; word-break:break-all;">
                                {{ $url }}
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:18px 40px; background-color:#fafafa; border-top:1px solid #eee; font-size:12px; color:#888;" align="center">
                            &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
