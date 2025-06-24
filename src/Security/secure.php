<?php
// Fichier de sécurité de base

// Vérifications de sécurité basiques
$request_uri = $_SERVER['REQUEST_URI'] ?? '';
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

// Blocage des tentatives d'injection SQL basiques
$sql_patterns = [
    '/(\%27)|(\')|(\-\-)|(\%23)|(#)/i',
    '/(union).*(select)/i',
    '/(drop|truncate|delete).*(table|from)/i'
];

foreach ($sql_patterns as $pattern) {
    if (preg_match($pattern, $request_uri) || preg_match($pattern, urldecode($request_uri))) {
        header('HTTP/1.0 403 Forbidden');
        exit('Tentative d\'injection détectée');
    }
}

// Blocage des tentatives XSS basiques
$xss_patterns = [
    '/<script/i',
    '/javascript:/i',
    '/<iframe/i'
];

foreach ($xss_patterns as $pattern) {
    if (preg_match($pattern, $request_uri) || preg_match($pattern, urldecode($request_uri))) {
        header('HTTP/1.0 403 Forbidden');
        exit('Tentative XSS détectée');
    }
} 