<?php
// Async email sender
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEmailAsync($to, $name, $otp) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->Timeout = 10;
        
        $mail->setFrom(MAIL_USERNAME, 'LGU1 System');
        $mail->addAddress($to, $name);
        $mail->isHTML(true);
        $mail->Subject = 'LGU1 Verification Code';
        
        $mail->Body = '<div style="font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto; padding: 20px;">
            <h2 style="color: #00473e; text-align: center;">LGU1 Verification</h2>
            <p>Hello ' . htmlspecialchars($name) . ',</p>
            <p>Your verification code:</p>
            <div style="text-align: center; margin: 20px 0;">
                <span style="background: #faae2b; color: #00473e; font-size: 24px; font-weight: bold; padding: 10px 20px; border-radius: 5px;">' . $otp . '</span>
            </div>
            <p style="font-size: 12px; color: #666;">Valid for 10 minutes</p>
        </div>';
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email send failed: " . $e->getMessage());
        return false;
    }
}

// If called directly with parameters
if (isset($argv[1]) && isset($argv[2]) && isset($argv[3])) {
    sendEmailAsync($argv[1], $argv[2], $argv[3]);
}
?>