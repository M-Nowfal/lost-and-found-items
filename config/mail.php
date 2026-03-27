<?php
/**
 * Mail Configuration
 * Lost & Found Portal
 * 
 * This file configures email sending using PHP mail() function
 * For production with SMTP, install PHPMailer via Composer
 */

// Mail configuration
define('MAIL_HOST', getenv('MAIL_HOST') ?: 'smtp.gmail.com');
define('MAIL_USERNAME', getenv('MAIL_USERNAME') ?: '');
define('MAIL_PASSWORD', getenv('MAIL_PASSWORD') ?: '');
define('MAIL_FROM', getenv('MAIL_FROM') ?: 'noreply@lostandfound.com');
define('MAIL_FROM_NAME', getenv('MAIL_FROM_NAME') ?: 'Lost & Found Portal');

// Enable/disable email (for development)
define('MAIL_ENABLED', getenv('MAIL_ENABLED') ?: false);

/**
 * Simple Mailer Class using PHP mail()
 * For full SMTP support, install PHPMailer via Composer:
 * composer require phpmailer/phpmailer
 */
class Mailer {
    
    public function send($to, $subject, $body, $isHtml = true) {
        if (!MAIL_ENABLED) {
            error_log("Email disabled - Would send to: {$to}, Subject: {$subject}");
            return true;
        }
        
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/' . ($isHtml ? 'html' : 'plain') . '; charset=UTF-8',
            'From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM . '>',
            'Reply-To: ' . MAIL_FROM,
            'X-Mailer: PHP/' . phpversion()
        ];
        
        return mail($to, $subject, $body, implode("\r\n", $headers));
    }
    
    public function sendMatchNotification($userEmail, $userName, $itemTitle, $matchType) {
        $subject = "Match Found for Your {$matchType} Item!";
        $body = $this->getMatchEmailTemplate($userName, $itemTitle, $matchType);
        return $this->send($userEmail, $subject, $body);
    }
    
    public function sendVerificationNotification($userEmail, $userName, $verified = true) {
        if ($verified) {
            $subject = "Your Account Has Been Verified!";
            $body = $this->getVerificationEmailTemplate($userName, true);
        } else {
            $subject = "Account Verification Pending";
            $body = $this->getVerificationEmailTemplate($userName, false);
        }
        return $this->send($userEmail, $subject, $body);
    }
    
    private function getMatchEmailTemplate($userName, $itemTitle, $matchType) {
        $color = $matchType === 'Lost' ? '#667EEA' : '#10B981';
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, {$color} 0%, " . ($matchType === 'Lost' ? '#764ba2' : '#38ef7d') . " 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .button { display: inline-block; background: linear-gradient(135deg, {$color} 0%, " . ($matchType === 'Lost' ? '#764ba2' : '#38ef7d') . " 100%); color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
                .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Match Found!</h1>
                </div>
                <div class='content'>
                    <p>Hello {$userName},</p>
                    <p>Great news! We found a potential match for your {$matchType} item:</p>
                    <p><strong>{$itemTitle}</strong></p>
                    <p>Please log in to your account to view the details and connect with the other party.</p>
                    <a href='" . (defined('BASE_URL') ? BASE_URL : '') . "' class='button'>View Match</a>
                </div>
                <div class='footer'>
                    <p>Lost & Found Portal - Helping you find what you've lost</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    private function getVerificationEmailTemplate($userName, $verified) {
        $color = $verified ? '#10B981' : '#F59E0B';
        $title = $verified ? 'Account Verified!' : 'Verification Pending';
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, {$color} 0%, " . ($verified ? '#38ef7d' : '#f97316') . " 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .button { display: inline-block; background: linear-gradient(135deg, {$color} 0%, " . ($verified ? '#38ef7d' : '#f97316') . " 100%); color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>{$title}</h1>
                </div>
                <div class='content'>
                    <p>Hello {$userName},</p>
                    " . ($verified 
                        ? '<p>Your account has been verified! You now have full access to the Lost & Found Portal.</p><a href="' . (defined('BASE_URL') ? BASE_URL : '') . '" class="button">Get Started</a>'
                        : '<p>Your account is pending verification. An admin will review your account shortly.</p>'
                    ) . "
                </div>
            </div>
        </body>
        </html>";
    }
}

function getMailer() {
    return new Mailer();
}
