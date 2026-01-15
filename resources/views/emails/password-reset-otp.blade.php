<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <div style="max-width: 500px; margin: 40px auto; background-color: #ffffff; padding: 40px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="text-align: center; margin-bottom: 30px;">
            <h2 style="color: #00473e; margin: 0; font-size: 24px;">LGU1 Password Reset</h2>
        </div>
        
        <p style="color: #333; font-size: 16px; margin-bottom: 10px;">Hello <strong>{{ $userName }}</strong>,</p>
        
        <p style="color: #666; font-size: 14px; line-height: 1.6; margin-bottom: 30px;">You requested to reset your password. Please use this verification code:</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <div style="display: inline-block; background-color: #faae2b; color: #00473e; font-size: 36px; font-weight: bold; padding: 20px 50px; border-radius: 8px; letter-spacing: 8px;">
                {{ $otp }}
            </div>
        </div>
        
        <p style="color: #fa5246; font-size: 13px; text-align: center; margin: 15px 0 30px 0;">Valid for 1 minute</p>
        
        <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid #e0e0e0;">
            <p style="color: #999; font-size: 12px; text-align: center; margin: 0; line-height: 1.6;">
                If you did not request this password reset, please ignore this email and your password will remain unchanged.
            </p>
            <p style="color: #999; font-size: 12px; text-align: center; margin: 15px 0 0 0;">
                <strong>Local Government Unit 1 - Authentication System</strong>
            </p>
        </div>
    </div>
</body>
</html>

