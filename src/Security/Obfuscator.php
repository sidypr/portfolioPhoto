<?php

namespace App\Security;

class Obfuscator
{
    /**
     * Génère une clé d'encodage basée sur la date et l'environnement
     * 
     * @return string
     */
    public static function generateKey(): string
    {
        $date = date('Ymd');
        $env = $_SERVER['APP_ENV'] ?? 'dev';
        return md5($date . '_' . $env . '_' . __DIR__);
    }
    
    /**
     * Encode une chaîne de caractères avec une clé
     * 
     * @param string $string
     * @return string
     */
    public static function encode(string $string): string
    {
        $key = self::generateKey();
        $result = '';
        
        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keyChar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) + ord($keyChar));
            $result .= $char;
        }
        
        return base64_encode($result);
    }
    
    /**
     * Décode une chaîne de caractères avec une clé
     * 
     * @param string $string
     * @return string
     */
    public static function decode(string $string): string
    {
        $key = self::generateKey();
        $string = base64_decode($string);
        $result = '';
        
        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keyChar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) - ord($keyChar));
            $result .= $char;
        }
        
        return $result;
    }
    
    /**
     * Vérifie que le code n'a pas été modifié
     * 
     * @param string $filePath
     * @param string $checksum
     * @return bool
     */
    public static function verifyIntegrity(string $filePath, string $checksum): bool
    {
        if (!file_exists($filePath)) {
            return false;
        }
        
        $content = file_get_contents($filePath);
        $calculatedChecksum = md5($content);
        
        return $calculatedChecksum === $checksum;
    }
} 