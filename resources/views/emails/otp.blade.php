<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ekscoder Login OTP</title>
</head>
<body style="margin: 0; padding: 0; background-color: #0a0a0f; font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #f8fafc;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #0a0a0f; padding: 40px 10px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 500px; background-color: #111118; border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 16px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.5);">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 28px 32px 20px 32px; text-align: center; border-bottom: 1px solid rgba(255, 255, 255, 0.06);">
                            <div style="font-size: 24px; font-weight: 900; color: #ffffff; letter-spacing: -0.5px;">
                                EKSCODER<span style="color: #b8ff00;">.</span>
                            </div>
                            <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 1.5px; margin-top: 4px;">
                                Security Verification
                            </div>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 32px;">
                            <h2 style="margin: 0 0 12px 0; font-size: 20px; font-weight: 800; color: #f8fafc; text-align: center;">
                                Login OTP Code 🔐
                            </h2>
                            <p style="margin: 0 0 24px 0; font-size: 14px; line-height: 1.6; color: #94a3b8; text-align: center;">
                                Hello, <strong style="color: #f8fafc;">{{ $name }}</strong>! You are receiving this email because a login attempt was made for your Ekscoder Admin account.
                            </p>

                            <!-- OTP Box -->
                            <div style="background-color: #16161f; border: 1px solid rgba(184, 255, 0, 0.35); border-radius: 14px; padding: 22px; text-align: center; margin-bottom: 24px; box-shadow: 0 0 20px rgba(184, 255, 0, 0.08);">
                                <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 8px;">
                                    Your 6-Digit Verification Code
                                </div>
                                <div style="font-family: 'Courier New', Courier, monospace; font-size: 38px; font-weight: 900; color: #b8ff00; letter-spacing: 10px; margin: 0;">
                                    {{ $otpCode }}
                                </div>
                            </div>

                            <p style="margin: 0 0 12px 0; font-size: 13px; line-height: 1.5; color: #94a3b8; text-align: center;">
                                ⏱️ This code is valid for <strong style="color: #f8fafc;">10 minutes</strong>.
                            </p>
                            <p style="margin: 0; font-size: 12px; line-height: 1.5; color: #64748b; text-align: center;">
                                If you did not request this code, please ignore this email or secure your account.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 18px 32px; background-color: #0d0d13; text-align: center; border-top: 1px solid rgba(255, 255, 255, 0.06); font-size: 12px; color: #64748b;">
                            &copy; {{ date('Y') }} Ekscoder Admin &bull; Automated Security Console
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
