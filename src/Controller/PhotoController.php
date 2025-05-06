<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Form\PhotoType;
use App\Repository\PhotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/photos')]
#[IsGranted('ROLE_ADMIN')]
class PhotoController extends AbstractController
{
    #[Route('/', name: 'app_photo_index', methods: ['GET'])]
    public function index(PhotoRepository $photoRepository): Response
    {
        return $this->render('photo/index.html.twig', [
            'photos' => $photoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_photo_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $photo = new Photo();
        $form = $this->createForm(PhotoType::class, $photo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('photos_directory'),
                    $newFilename
                );

                $photo->setFilename($newFilename);
            }

            // Si cette photo est définie comme arrière-plan, désactiver les autres
            if ($photo->isBackground()) {
                $this->resetOtherBackgrounds($entityManager);
            }
            
            $entityManager->persist($photo);
            $entityManager->flush();

            $this->addFlash('success', 'La photo a été ajoutée avec succès.');

            return $this->redirectToRoute('app_photo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('photo/new.html.twig', [
            'photo' => $photo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_photo_show', methods: ['GET'])]
    public function show(Photo $photo): Response
    {
        return $this->render('photo/show.html.twig', [
            'photo' => $photo,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_photo_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Photo $photo, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(PhotoType::class, $photo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('photos_directory'),
                    $newFilename
                );

                // Supprimer l'ancienne image si elle existe
                if ($photo->getFilename()) {
                    $oldFilePath = $this->getParameter('photos_directory') . '/' . $photo->getFilename();
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }

                $photo->setFilename($newFilename);
            }

            // Si cette photo est définie comme arrière-plan, désactiver les autres
            if ($photo->isBackground()) {
                $this->resetOtherBackgrounds($entityManager, $photo->getId());
            }

            $entityManager->flush();

            $this->addFlash('success', 'La photo a été modifiée avec succès.');

            return $this->redirectToRoute('app_photo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('photo/edit.html.twig', [
            'photo' => $photo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_photo_delete', methods: ['POST'])]
    public function delete(Request $request, Photo $photo, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$photo->getId(), $request->request->get('_token'))) {
            // Supprimer l'image si elle existe
            if ($photo->getFilename()) {
                $filePath = $this->getParameter('photos_directory') . '/' . $photo->getFilename();
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $entityManager->remove($photo);
            $entityManager->flush();

            $this->addFlash('success', 'La photo a été supprimée avec succès.');
        }

        return $this->redirectToRoute('app_photo_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Réinitialise tous les arrière-plans sauf celui qui est spécifié
     */
    private function resetOtherBackgrounds(EntityManagerInterface $entityManager, ?int $exceptId = null): void
    {
        $qb = $entityManager->createQueryBuilder()
            ->update('App\Entity\Photo', 'p')
            ->set('p.isBackground', 'false')
            ->where('p.isBackground = true');
        
        if ($exceptId !== null) {
            $qb->andWhere('p.id != :id')
               ->setParameter('id', $exceptId);
        }
        
        $qb->getQuery()->execute();
    }
} 