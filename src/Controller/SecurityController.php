<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Rediriger vers le tableau de bord si l'utilisateur est déjà connecté
        if ($this->getUser()) {
            return $this->redirectToRoute('app_admin_dashboard');
        }

        // Récupérer l'erreur de connexion s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();
        
        // Dernier nom d'utilisateur saisi
        $lastUsername = $authenticationUtils->getLastUsername();

        // Affichage d'un message d'erreur explicite pour le débogage
        $errorMessage = null;
        if ($error) {
            $errorMessage = 'Erreur d\'authentification: ' . $error->getMessage();
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername ?? '',
            'error' => $error,
            'error_message' => $errorMessage,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Cette méthode peut rester vide - elle sera interceptée par la configuration de sécurité
    }
} 