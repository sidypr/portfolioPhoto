<?php

namespace App\Controller;

use App\Repository\PhotoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin_dashboard')]
    public function dashboard(PhotoRepository $photoRepository): Response
    {
        // Vérification que l'utilisateur a le rôle ADMIN
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        return $this->render('admin/dashboard.html.twig', [
            'photos_count' => count($photoRepository->findAll()),
            'latest_photos' => $photoRepository->findBy([], ['id' => 'DESC'], 5),
        ]);
    }
} 