<?php

namespace App\Controller;

use App\Repository\ContentRepository;
use App\Repository\PhotoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PhotoRepository $photoRepository): Response
    {
        return $this->render('main/index.html.twig', [
            'featured_photos' => $photoRepository->findLatest(6),
            'background_photo' => $photoRepository->findOneByIsBackground(),
        ]);
    }

    #[Route('/galerie', name: 'app_gallery')]
    public function gallery(PhotoRepository $photoRepository): Response
    {
        return $this->render('main/gallery.html.twig', [
            'photos' => $photoRepository->findAll(),
            'categories' => $this->getCategories($photoRepository),
            'background_photo' => $photoRepository->findOneByIsBackground(),
        ]);
    }

    #[Route('/galerie/{category}', name: 'app_gallery_category')]
    public function galleryByCategory(string $category, PhotoRepository $photoRepository): Response
    {
        return $this->render('main/gallery.html.twig', [
            'photos' => $photoRepository->findByCategory($category),
            'categories' => $this->getCategories($photoRepository),
            'current_category' => $category,
            'background_photo' => $photoRepository->findOneByIsBackground(),
        ]);
    }

    #[Route('/a-propos', name: 'app_about')]
    public function about(PhotoRepository $photoRepository, ContentRepository $contentRepository): Response
    {
        // Charger les blocs de contenu pour la page "À propos"
        $aboutImage = $contentRepository->findOrCreateByIdentifier('about_image', 'Image À propos', '', 'A propos');
        $aboutHistory = $contentRepository->findOrCreateByIdentifier('about_history', 'Notre histoire', 'Depuis notre création en 2010, Portfolio Photo s\'est engagé à offrir des services de photographie de la plus haute qualité.', 'A propos');
        $aboutMission = $contentRepository->findOrCreateByIdentifier('about_mission', 'Notre mission', 'Nous nous engageons à fournir des services de photographie exceptionnels', 'A propos');
        $teamMembers = [];
        
        for ($i = 1; $i <= 4; $i++) {
            $teamMembers[] = $contentRepository->findOrCreateByIdentifier(
                'team_member_' . $i,
                'Membre de l\'équipe ' . $i,
                'Description du membre ' . $i,
                'Équipe'
            );
        }
        
        return $this->render('main/about.html.twig', [
            'background_photo' => $photoRepository->findOneByIsBackground(),
            'about_image' => $aboutImage,
            'about_history' => $aboutHistory,
            'about_mission' => $aboutMission,
            'team_members' => $teamMembers,
        ]);
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(PhotoRepository $photoRepository, ContentRepository $contentRepository): Response
    {
        // Charger les blocs de contenu pour la page "Contact"
        $contactInfo = $contentRepository->findOrCreateByIdentifier('contact_info', 'Informations de contact', '<p><i class="bi bi-geo-alt me-2"></i> 123 Avenue de la Photographie, 75000 Paris</p><p><i class="bi bi-telephone me-2"></i> +33 1 23 45 67 89</p><p><i class="bi bi-envelope me-2"></i> contact@portfolio-photo.fr</p>', 'Contact');
        $contactHours = $contentRepository->findOrCreateByIdentifier('contact_hours', 'Heures d\'ouverture', '<p>Lundi - Vendredi : 9h00 - 18h00</p><p>Samedi : 10h00 - 16h00</p><p>Dimanche : Fermé</p>', 'Contact');
        
        return $this->render('main/contact.html.twig', [
            'background_photo' => $photoRepository->findOneByIsBackground(),
            'contact_info' => $contactInfo,
            'contact_hours' => $contactHours,
        ]);
    }

    private function getCategories(PhotoRepository $photoRepository): array
    {
        $photos = $photoRepository->findAll();
        $categories = [];

        foreach ($photos as $photo) {
            $category = $photo->getCategory();
            if (!in_array($category, $categories)) {
                $categories[] = $category;
            }
        }

        return $categories;
    }
} 