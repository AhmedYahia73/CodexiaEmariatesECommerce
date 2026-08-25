<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased;">

    <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f4f6f9; padding: 40px 10px;">
        <tr>
            <td align="center">
                
                <!-- Main Email Card -->
                <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 520px; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden;">
                    
                    <!-- Header Accent Bar -->
                    <tr>
                        <td style="height: 6px; background: linear-gradient(90deg, #4f46e5, #6366f1);"></td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 32px 32px 32px; text-align: center;">
                            
                           

                            <!-- Title -->
                            <h1 style="font-size: 20px; font-weight: 700; color: #0f172a; margin: 0 0 12px 0;">
                                Reset Your Password
                            </h1>
                            
                            <!-- Subtitle / Description -->
                            <p style="font-size: 15px; color: #475569; line-height: 1.6; margin: 0 0 28px 0;">
                                We received a request to reset the password for your account. Use the verification code below to proceed with setting a new password.
                            </p>

                            <!-- Password Reset Code Container -->
                            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 28px;">
                                <span style="display: block; font-size: 12px; font-weight: 600; text-transform: uppercase; color: #64748b; letter-spacing: 1px; margin-bottom: 8px;">Password Reset Code</span>
                                <span style="font-family: 'Courier New', Courier, monospace; font-size: 32px; font-weight: 700; color: #4f46e5; letter-spacing: 8px;">{{ $code }}</span>
                            </div>

                            <!-- Expiration & Security Notice -->
                            <p style="font-size: 13px; color: #64748b; margin: 0 0 24px 0; line-height: 1.5;">
                                This code will expire in <strong style="color: #334155;">10 minutes</strong>.<br>
                                If you did not request a password reset, please ignore this email or contact support if you have concerns.
                            </p>

                            <!-- Subtle Divider -->
                            <div style="height: 1px; background-color: #f1f5f9; margin-bottom: 24px;"></div>

                            <!-- Footer Note -->
                            <p style="font-size: 12px; color: #94a3b8; margin: 0;">
                                &copy; {{ date('Y') }} YourApp. All rights reserved.
                            </p>

                        </td>
                    </tr>
 
                </table>
                <!-- End Main Card -->

            </td>
        </tr>
    </table>

</body>
</html>