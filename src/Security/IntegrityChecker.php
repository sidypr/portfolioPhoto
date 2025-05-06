<?php

namespace App\Security;

class IntegrityChecker
{
    private const CHECKSUMS_FILE = __DIR__ . '/../../config/checksums.php';
    
    /**
     * Vérifie l'intégrité des fichiers critiques
     * 
     * @return array Tableau des erreurs
     */
    public static function checkIntegrity(): array
    {
        $errors = [];
        
        if (!file_exists(self::CHECKSUMS_FILE)) {
            $errors[] = 'Le fichier de checksums est manquant. Lancez bin/generate-checksums pour le recréer.';
            return $errors;
        }
        
        $storedChecksums = require self::CHECKSUMS_FILE;
        
        if (!is_array($storedChecksums)) {
            $errors[] = 'Le fichier de checksums est corrompu. Lancez bin/generate-checksums pour le recréer.';
            return $errors;
        }
        
        $baseDir = dirname(dirname(__DIR__));
        
        foreach ($storedChecksums as $file => $checksum) {
            $filePath = $baseDir . '/' . $file;
            
            if (!file_exists($filePath)) {
                $errors[] = "Fichier manquant: $file";
                continue;
            }
            
            $currentChecksum = md5(file_get_contents($filePath));
            
            if ($currentChecksum !== $checksum) {
                $errors[] = "Fichier modifié: $file";
            }
        }
        
        return $errors;
    }
    
    /**
     * Vérifie l'intégrité et bloque l'exécution si des modifications non autorisées sont détectées
     * 
     * @param bool $silent Si true, ne génère pas d'erreur visible
     * @return bool
     */
    public static function enforceIntegrity(bool $silent = false): bool
    {
        $errors = self::checkIntegrity();
        
        if (!empty($errors)) {
            if (!$silent) {
                header('HTTP/1.0 403 Forbidden');
                echo '<h1>Erreur de sécurité</h1>';
                echo '<p>Des modifications non autorisées ont été détectées. Contactez l\'administrateur.</p>';
                
                if ($_SERVER['APP_ENV'] === 'dev') {
                    echo '<ul>';
                    foreach ($errors as $error) {
                        echo "<li>$error</li>";
                    }
                    echo '</ul>';
                    
                    echo '<p>Pour régénérer les checksums, exécutez : <code>bin/generate-checksums</code></p>';
                }
                
                exit;
            }
            
            return false;
        }
        
        return true;
    }
} 