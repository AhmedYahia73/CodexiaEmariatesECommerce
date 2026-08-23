<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email Address</title>
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
                            
                            <!-- Logo / App Name -->
                            <div style="font-size: 24px; font-weight: 800; color: #1e293b; letter-spacing: -0.5px; margin-bottom: 24px;">
                                <span style="color: #4f46e5;">.</span>
                            </div>

                            <!-- Greeting & Title -->
                            <h1 style="font-size: 20px; font-weight: 700; color: #0f172a; margin: 0 0 12px 0;">
                                Verify your email address
                            </h1>
                            <p style="font-size: 15px; color: #475569; line-height: 1.6; margin: 0 0 28px 0;">
                                Thank you for signing up! Please use the verification code below to complete your registration and activate your account.
                            </p>

                            <!-- Verification Code Container -->
                            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 28px;">
                                <span style="display: block; font-size: 12px; font-weight: 600; text-transform: uppercase; color: #64748b; letter-spacing: 1px; margin-bottom: 8px;">Your Verification Code</span>
                                <span style="font-family: 'Courier New', Courier, monospace; font-size: 32px; font-weight: 700; color: #4f46e5; letter-spacing: 8px;">{{ $code }}</span>
                            </div>

                            <!-- Expiration Warning -->
                            <p style="font-size: 13px; color: #64748b; margin: 0 0 24px 0;">
                                This code will expire in <strong style="color: #334155;">10 minutes</strong>. If you didn't request this email, you can safely ignore it.
                            </p>

                            <!-- Subtle Divider -->
                            <div style="height: 1px; background-color: #f1f5f9; margin-bottom: 24px;"></div>
 

                        </td>
                    </tr>
 
                </table>
                <!-- End Main Card -->

            </td>
        </tr>
    </table>

</body>
</html>