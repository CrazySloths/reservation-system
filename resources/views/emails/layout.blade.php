<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LGU Notification')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', 'Arial', sans-serif;
            background-color: #f3f4f6;
            padding: 20px;
            line-height: 1.6;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .email-header {
            background-color: #0f3d3e;
            padding: 30px 20px;
            text-align: center;
            color: #ffffff;
        }
        
        .email-header .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
            background-color: #ffffff;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            font-weight: bold;
            color: #0f3d3e;
        }
        
        .email-header h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .email-header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .email-body {
            padding: 40px 30px;
            color: #1f2937;
        }
        
        .email-body h2 {
            font-size: 20px;
            font-weight: 600;
            color: #0f3d3e;
            margin-bottom: 20px;
        }
        
        .email-body p {
            font-size: 15px;
            margin-bottom: 15px;
            color: #4b5563;
        }
        
        .info-box {
            background-color: #f0fdfa;
            border-left: 4px solid #14b8a6;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
        }
        
        .info-box-warning {
            background-color: #fffbeb;
            border-left-color: #f59e0b;
        }
        
        .info-box-error {
            background-color: #fef2f2;
            border-left-color: #ef4444;
        }
        
        .info-box-success {
            background-color: #f0fdf4;
            border-left-color: #10b981;
        }
        
        .info-box h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #0f3d3e;
        }
        
        .info-box p {
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .info-box strong {
            color: #0f3d3e;
            font-weight: 600;
        }
        
        .button {
            display: inline-block;
            padding: 14px 32px;
            background-color: #0f3d3e;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            margin: 20px 0;
            transition: background-color 0.3s;
        }
        
        .button:hover {
            background-color: #1a5f5f;
        }
        
        .button-secondary {
            background-color: #14b8a6;
        }
        
        .button-secondary:hover {
            background-color: #0d9488;
        }
        
        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 30px 0;
        }
        
        .email-footer {
            background-color: #f9fafb;
            padding: 25px 30px;
            text-align: center;
            font-size: 13px;
            color: #6b7280;
        }
        
        .email-footer p {
            margin-bottom: 10px;
        }
        
        .email-footer a {
            color: #0f3d3e;
            text-decoration: none;
            font-weight: 600;
        }
        
        .countdown-timer {
            background-color: #f59e0b;
            color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 25px 0;
        }
        
        .countdown-timer h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        
        .countdown-timer .time {
            font-size: 32px;
            font-weight: 700;
            margin: 10px 0;
        }
        
        @media only screen and (max-width: 600px) {
            .email-body {
                padding: 30px 20px;
            }
            
            .email-header h1 {
                font-size: 20px;
            }
            
            .button {
                display: block;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            {{-- 
                CSS-based logo for localhost/development.
                
                PRODUCTION: Replace with actual logo URL when deployed:
                <img src="{{ config('app.url') }}/assets/images/logo.png" alt="LGU Logo" style="width: 80px; height: 80px; border-radius: 50%;">
            --}}
            <div style="width: 80px; height: 80px; margin: 0 auto 15px; background: linear-gradient(135deg, #ffffff 0%, #e0f2f1 100%); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border: 3px solid rgba(20, 184, 166, 0.3);">
                <div style="text-align: center; line-height: 1.1;">
                    <div style="font-size: 24px; font-weight: 800; color: #0f3d3e; letter-spacing: 0.5px; margin-bottom: 2px;">LGU</div>
                    <div style="font-size: 8px; font-weight: 600; color: #14b8a6; text-transform: uppercase; letter-spacing: 1.5px;">SYSTEM</div>
                </div>
            </div>
            <h1 style="color: #ffffff; font-size: 24px; font-weight: 700; margin-bottom: 5px; margin-top: 0;">Local Government Unit</h1>
            <p style="color: #ffffff; font-size: 14px; opacity: 0.95; margin: 0;">Facilities Reservation System</p>
        </div>
        
        <!-- Body -->
        <div class="email-body">
            @yield('content')
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <p><strong>Local Government Unit - Facilities Management</strong></p>
            <p>This is an automated notification from the LGU Facilities Reservation System.</p>
            <p>For assistance, please contact: <a href="mailto:facilities@lgu.gov.ph">facilities@lgu.gov.ph</a></p>
            <p style="margin-top: 15px; font-size: 12px; color: #9ca3af;">
                © {{ date('Y') }} Local Government Unit. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>

