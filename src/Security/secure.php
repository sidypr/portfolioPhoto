<?php
// Fichier de sécurité pour détecter et prévenir les tentatives d'intrusion

namespace App\Security;

class SecurityMonitor {
    private $logFile;
    private $ipAddress;
    private $requestUri;
    private $userAgent;
    private $requestMethod;
    
    public function __construct() {
        $this->logFile = dirname(dirname(__DIR__)) . '/var/log/security.log';
        $this->ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $this->requestUri = $_SERVER['REQUEST_URI'] ?? 'Unknown';
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $this->requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'Unknown';
    }
    
    public function checkSecurity() {
        // Vérification basique des injections SQL
        $this->checkForSQLInjection();
        
        // Vérification des tentatives XSS
        $this->checkForXSS();
        
        // Vérification des path traversal
        $this->checkForPathTraversal();
    }
    
    private function checkForSQLInjection() {
        $patterns = [
            '/(\%27)|(\')|(\-\-)|(\%23)|(#)/i',
            '/((\%3D)|(=))[^\n]*((\%27)|(\')|(\-\-)|(\%3B)|(;))/i',
            '/\w*((\%27)|(\'))((\%6F)|o|(\%4F))((\%72)|r|(\%52))/i',
            '/(union).*(select)/i',
            '/(select|insert|update|delete|drop|truncate).*(from|into|table)/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $this->requestUri) || 
                preg_match($pattern, urldecode($this->requestUri))) {
                $this->logAttack('SQL Injection');
            }
        }
    }
    
    private function checkForXSS() {
        $patterns = [
            '/<script\b[^>]*>(.*?)<\/script>/i',
            '/javascript\s*:/i',
            '/<\s*img[^>]+src\s*=\s*["\']?\s*javascript\s*:/i',
            '/<\s*iframe/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $this->requestUri) || 
                preg_match($pattern, urldecode($this->requestUri))) {
                $this->logAttack('XSS Attack');
            }
        }
    }
    
    private function checkForPathTraversal() {
        $patterns = [
            '/\.\.\//i',
            '/\.\.\\\/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $this->requestUri) || 
                preg_match($pattern, urldecode($this->requestUri))) {
                $this->logAttack('Path Traversal');
            }
        }
    }
    
    private function logAttack($type) {
        $date = date('Y-m-d H:i:s');
        $logMessage = "[$date] Attack Type: $type | IP: {$this->ipAddress} | URI: {$this->requestUri} | User Agent: {$this->userAgent} | Method: {$this->requestMethod}\n";
        
        // Assurez-vous que le répertoire de log existe
        $dir = dirname($this->logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
        
        // Bloquer l'accès en cas d'attaque
        header('HTTP/1.0 403 Forbidden');
        exit('Accès refusé - Tentative d\'intrusion détectée');
    }
}

// Utilisation automatique à chaque requête
$security = new SecurityMonitor();
$security->checkSecurity(); 