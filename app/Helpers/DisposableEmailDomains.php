<?php

namespace App\Helpers;

class DisposableEmailDomains
{
    /**
     * ALLOWLIST: Only legitimate/legal email providers are accepted
     * This is MORE SECURE than blocking disposable domains because
     * new temporary email services appear daily and we can't keep up.
     * 
     * Strategy: Only allow well-known, trusted email providers
     */
    private static $allowedDomains = [
        // ==================== MAJOR EMAIL PROVIDERS ====================
        
        // Google
        'gmail.com', 'googlemail.com',
        
        // Microsoft
        'outlook.com', 'outlook.ph', 'hotmail.com', 'hotmail.ph', 'live.com', 
        'live.ph', 'msn.com', 'passport.com',
        
        // Yahoo
        'yahoo.com', 'yahoo.com.ph', 'yahoo.ph', 'ymail.com', 'rocketmail.com',
        
        // Apple
        'icloud.com', 'me.com', 'mac.com',
        
        // ==================== PRIVACY-FOCUSED PROVIDERS ====================
        
        'protonmail.com', 'proton.me', 'pm.me',
        'tutanota.com', 'tuta.io',
        'mailfence.com',
        'startmail.com',
        'posteo.de', 'posteo.net',
        'runbox.com',
        
        // ==================== POPULAR INTERNATIONAL PROVIDERS ====================
        
        // European
        'gmx.com', 'gmx.net', 'gmx.de',
        'web.de',
        'mail.com',
        'zoho.com', 'zohomail.com',
        
        // Asian
        'qq.com', '163.com', '126.com', // China
        'naver.com', 'hanmail.net', 'daum.net', // Korea
        'rediffmail.com', // India
        
        // Russian
        'yandex.com', 'yandex.ru', 'ya.ru',
        'mail.ru',
        
        // ==================== CORPORATE/BUSINESS PROVIDERS ====================
        
        'aol.com',
        'fastmail.com', 'fastmail.fm',
        'hushmail.com',
        'inbox.com',
        'lavabit.com',
        
        // ==================== PHILIPPINE ISP PROVIDERS ====================
        
        'pldt.net', 'pldtdsl.net',
        'globe.com.ph',
        'smart.com.ph',
        'convergeict.com',
        'sky.net.ph', 'skycable.net',
        
        // ==================== EDUCATIONAL INSTITUTIONS ====================
        // Note: You can add specific .edu.ph domains as needed
        
        // ==================== CUSTOM DOMAINS ====================
        // Note: If users have legitimate custom domains, add them here
        
        // Add more as needed...
    ];

    /**
     * Check if an email domain is from an allowed/legitimate provider
     *
     * @param string $email
     * @return bool
     */
    public static function isAllowed($email)
    {
        $domain = self::getDomain($email);
        return in_array($domain, self::$allowedDomains, true);
    }

    /**
     * Check if an email looks suspicious (pattern detection)
     * Catches disposable emails created using legitimate domains
     * Example: ma.rion.ano.n.uevo71@googlemail.com (Emailnator)
     *
     * @param string $email
     * @return bool
     */
    public static function hasSuspiciousPattern($email)
    {
        // TEMPORARILY DISABLED FOR TESTING - Allow all patterns
        // This can be re-enabled later after user registration is complete
        return false;
        
        /* ORIGINAL VALIDATION - COMMENTED OUT FOR NOW
        // Get the local part (before @)
        $localPart = substr($email, 0, strpos($email, '@'));
        
        // Suspicious Pattern 1: Too many dots (e.g., ma.rion.ano.n.uevo71)
        $dotCount = substr_count($localPart, '.');
        if ($dotCount >= 3) {
            return true; // Likely Emailnator or similar
        }
        
        // Suspicious Pattern 2: Local part is too long (> 25 characters)
        if (strlen($localPart) > 25) {
            return true;
        }
        
        // Suspicious Pattern 3: Ends with many numbers (e.g., user12345678)
        if (preg_match('/\d{6,}$/', $localPart)) {
            return true;
        }
        
        // Suspicious Pattern 4: Random-looking strings with numbers scattered
        // Example: a1b2c3d4e5 or random123letters456
        $alphaCount = preg_match_all('/[a-zA-Z]/', $localPart);
        $digitCount = preg_match_all('/\d/', $localPart);
        if ($alphaCount > 0 && $digitCount > 0) {
            $ratio = $digitCount / strlen($localPart);
            if ($ratio > 0.7) { // More than 70% digits (increased from 30% to allow legitimate usernames with numbers)
                return true;
            }
        }
        
        // Suspicious Pattern 5: Very short local part (1-2 characters) + numbers
        if (preg_match('/^[a-z]{1,2}\d+$/', $localPart)) {
            return true;
        }
        
        return false;
        */
    }

    /**
     * Check if an email domain is NOT allowed OR has suspicious patterns
     *
     * @param string $email
     * @return bool
     */
    public static function isDisposable($email)
    {
        // Check 1: Is domain allowed?
        if (!self::isAllowed($email)) {
            return true;
        }
        
        // Check 2: Even if domain is allowed, check for suspicious patterns
        if (self::hasSuspiciousPattern($email)) {
            return true;
        }
        
        return false;
    }

    /**
     * Get the domain from an email address
     *
     * @param string $email
     * @return string
     */
    public static function getDomain($email)
    {
        return strtolower(substr(strrchr($email, "@"), 1));
    }
    
    /**
     * Get all allowed domains (for reference/debugging)
     *
     * @return array
     */
    public static function getAllowedDomains()
    {
        return self::$allowedDomains;
    }
}

