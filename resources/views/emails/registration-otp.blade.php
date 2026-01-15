<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9;">
    <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h2 style="color: #00473e; text-align: center; margin-bottom: 20px;">Welcome to LGU1!</h2>
        <p style="color: #333; font-size: 16px;">Hello {{ $userName }},</p>
        <p style="color: #666; line-height: 1.6;">Thank you for registering with LGU1. Please verify your email address using the code below:</p>
        <div style="text-align: center; margin: 30px 0;">
            <div style="background: #faae2b; color: #00473e; font-size: 32px; font-weight: bold; padding: 15px 30px; border-radius: 8px; letter-spacing: 3px; display: inline-block;">{{ $otp }}</div>
            <p style="color: #fa5246; font-size: 12px; margin-top: 10px;">Valid for 1 minute</p>
        </div>
        <p style="color: #666; font-size: 14px;">If you did not register for an account, please ignore this email.</p>
        <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="color: #999; font-size: 12px; text-align: center;">Local Government Unit 1 - Authentication System</p>
    </div>
</div>