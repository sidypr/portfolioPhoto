<?php

namespace App\Controller;

use App\Entity\Content;
use App\Form\ContentType;
use App\Repository\ContentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/content')]
#[IsGranted('ROLE_ADMIN')]
class ContentController extends AbstractController
{
    #[Route('/', name: 'app_content_index', methods: ['GET'])]
    public function index(ContentRepository $contentRepository): Response
    {
        // Organiser le contenu par sections
        $contents = $contentRepository->findAll();
        $contentsBySection = [];
        
        foreach ($contents as $content) {
            $section = $content->getSection() ?: 'Non classé';
            $contentsBySection[$section][] = $content;
        }
        
        return $this->render('content/index.html.twig', [
            'contentsBySection' => $contentsBySection,
        ]);
    }

    #[Route('/new', name: 'app_content_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $content = new Content();
        $form = $this->createForm(ContentType::class, $content);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    // S'assurer que le répertoire existe
                    $contentImagesDir = $this->getParameter('content_images_directory');
                    if (!is_dir($contentImagesDir)) {
                        mkdir($contentImagesDir, 0777, true);
                    }

                    $imageFile->move(
                        $contentImagesDir,
                        $newFilename
                    );

                    $content->setImageFilename($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image : ' . $e->getMessage());
                    return $this->redirectToRoute('app_content_new');
                }
            }
            
            $content->updateTimestamp();
            $entityManager->persist($content);
            $entityManager->flush();

            $this->addFlash('success', 'Le contenu a été créé avec succès.');
            return $this->redirectToRoute('app_content_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('content/new.html.twig', [
            'content' => $content,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_content_show', methods: ['GET'])]
    public function show(Content $content): Response
    {
        return $this->render('content/show.html.twig', [
            'content' => $content,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_content_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Content $content, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ContentType::class, $content);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    // S'assurer que le répertoire existe
                    $contentImagesDir = $this->getParameter('content_images_directory');
                    if (!is_dir($contentImagesDir)) {
                        mkdir($contentImagesDir, 0777, true);
                    }

                    $imageFile->move(
                        $contentImagesDir,
                        $newFilename
                    );

                    // Supprimer l'ancienne image si elle existe
                    if ($content->getImageFilename()) {
                        $oldFilePath = $contentImagesDir . '/' . $content->getImageFilename();
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                        }
                    }

                    $content->setImageFilename($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image : ' . $e->getMessage());
                    return $this->redirectToRoute('app_content_edit', ['id' => $content->getId()]);
                }
            }
            
            $content->updateTimestamp();
            $entityManager->flush();

            $this->addFlash('success', 'Le contenu a été modifié avec succès.');
            return $this->redirectToRoute('app_content_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('content/edit.html.twig', [
            'content' => $content,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_content_delete', methods: ['POST'])]
    public function delete(Request $request, Content $content, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$content->getId(), $request->request->get('_token'))) {
            try {
                // Supprimer l'image si elle existe
                if ($content->getImageFilename()) {
                    $contentImagesDir = $this->getParameter('content_images_directory');
                    $filePath = $contentImagesDir . '/' . $content->getImageFilename();
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }

                $entityManager->remove($content);
                $entityManager->flush();

                $this->addFlash('success', 'Le contenu a été supprimé avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
            }
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('app_content_index', [], Response::HTTP_SEE_OTHER);
    }
} 