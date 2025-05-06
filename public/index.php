<?php

// Vérification de sécurité de base
require dirname(__DIR__).'/src/Security/secure.php';

// Vérification de l'intégrité du code
if (file_exists(dirname(__DIR__).'/src/Security/IntegrityChecker.php')) {
    require_once dirname(__DIR__).'/src/Security/IntegrityChecker.php';
    
    if (class_exists('App\Security\IntegrityChecker')) {
        // En mode production, vérifier l'intégrité des fichiers critiques
        if ($_SERVER['APP_ENV'] === 'prod') {
            \App\Security\IntegrityChecker::enforceIntegrity();
        }
    }
}

// Vérification de la licence
if (file_exists(dirname(__DIR__).'/src/Security/LicenseChecker.php')) {
    require_once dirname(__DIR__).'/src/Security/LicenseChecker.php';
    
    if (class_exists('App\Security\LicenseChecker')) {
        // En mode production, vérifier la validité de la licence
        if ($_SERVER['APP_ENV'] === 'prod') {
            if (!\App\Security\LicenseChecker::isLicenseValid()) {
                header('HTTP/1.0 403 Forbidden');
                exit('Licence non valide. Contactez l\'administrateur.');
            }
        }
    }
}

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    // Protection anti-vol supplémentaire - Vérification du domaine
    $allowedDomains = ['localhost', '127.0.0.1', 'sidypr.com', $context['SERVER_NAME'] ?? ''];
    if ($context['APP_ENV'] === 'prod' && !in_array($context['SERVER_NAME'] ?? '', $allowedDomains)) {
        header('HTTP/1.0 403 Forbidden');
        exit('Domaine non autorisé');
    }
    
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
