<?php

namespace App\Security;

class LicenseChecker
{
    private const LICENSE_FILE = __DIR__ . '/../../config/license.key';
    private const LICENSE_CHECK_URL = 'https://example.com/verify-license'; // À remplacer par votre URL réelle
    private const DOMAIN_KEY = 'portfolio_photo_domain';
    private const OWNER_KEY = 'portfolio_photo_owner';
    private const EXPIRY_KEY = 'portfolio_photo_expiry';
    
    /**
     * Vérifie si la licence est valide
     * 
     * @return bool
     */
    public static function isLicenseValid(): bool
    {
        // Vérification locale
        if (!self::hasValidLicenseFile()) {
            return false;
        }
        
        // Empêche l'utilisation sur un domaine non autorisé
        if (!self::isValidDomain()) {
            return false;
        }
        
        // Vérification de l'expiration
        if (!self::checkLicenseExpiry()) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Vérifie si le fichier de licence existe et est valide
     * 
     * @return bool
     */
    private static function hasValidLicenseFile(): bool
    {
        if (!file_exists(self::LICENSE_FILE)) {
            return false;
        }
        
        $licenseContent = file_get_contents(self::LICENSE_FILE);
        $licenseData = json_decode($licenseContent, true);
        
        if (!$licenseData || !isset($licenseData['signature'])) {
            return false;
        }
        
        // Vérification de la signature
        $dataToVerify = [
            'domain' => $licenseData[self::DOMAIN_KEY] ?? '',
            'owner' => $licenseData[self::OWNER_KEY] ?? '',
            'expiry' => $licenseData[self::EXPIRY_KEY] ?? '',
        ];
        
        $signature = md5(json_encode($dataToVerify) . '_sidypr_portfolio_photo');
        
        return $signature === $licenseData['signature'];
    }
    
    /**
     * Vérifie si le domaine actuel est autorisé
     * 
     * @return bool
     */
    private static function isValidDomain(): bool
    {
        if (!file_exists(self::LICENSE_FILE)) {
            return false;
        }
        
        $licenseContent = file_get_contents(self::LICENSE_FILE);
        $licenseData = json_decode($licenseContent, true);
        
        if (!$licenseData || !isset($licenseData[self::DOMAIN_KEY])) {
            return false;
        }
        
        $allowedDomain = $licenseData[self::DOMAIN_KEY];
        $currentDomain = $_SERVER['HTTP_HOST'] ?? 'localhost';
        
        // Autorise localhost pour le développement et le domaine spécifié dans la licence
        return $currentDomain === 'localhost' || $currentDomain === '127.0.0.1' || $currentDomain === $allowedDomain;
    }
    
    /**
     * Vérifie si la licence n'a pas expiré
     * 
     * @return bool
     */
    private static function checkLicenseExpiry(): bool
    {
        if (!file_exists(self::LICENSE_FILE)) {
            return false;
        }
        
        $licenseContent = file_get_contents(self::LICENSE_FILE);
        $licenseData = json_decode($licenseContent, true);
        
        if (!$licenseData || !isset($licenseData[self::EXPIRY_KEY])) {
            return false;
        }
        
        $expiryDate = $licenseData[self::EXPIRY_KEY];
        $currentDate = date('Y-m-d');
        
        return $currentDate <= $expiryDate;
    }
    
    /**
     * Génère un fichier de licence pour le développement local
     * 
     * @param string $owner
     * @param string $domain
     * @param string $expiryDate Format Y-m-d
     * @return bool
     */
    public static function generateLocalLicense(string $owner, string $domain, string $expiryDate): bool
    {
        $licenseData = [
            self::DOMAIN_KEY => $domain,
            self::OWNER_KEY => $owner,
            self::EXPIRY_KEY => $expiryDate,
        ];
        
        // Génération de la signature
        $signature = md5(json_encode($licenseData) . '_sidypr_portfolio_photo');
        $licenseData['signature'] = $signature;
        
        // Sauvegarde du fichier de licence
        $licenseDir = dirname(self::LICENSE_FILE);
        if (!is_dir($licenseDir)) {
            mkdir($licenseDir, 0755, true);
        }
        
        return file_put_contents(self::LICENSE_FILE, json_encode($licenseData)) !== false;
    }
} 