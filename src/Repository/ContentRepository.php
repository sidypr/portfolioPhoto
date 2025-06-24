<?php

namespace App\Repository;

use App\Entity\Content;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Content>
 *
 * @method Content|null find($id, $lockMode = null, $lockVersion = null)
 * @method Content|null findOneBy(array $criteria, array $orderBy = null)
 * @method Content[]    findAll()
 * @method Content[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Content::class);
    }

    /**
     * Trouve un contenu par son identifiant, le crée s'il n'existe pas
     */
    public function findOrCreateByIdentifier(string $identifier, string $defaultTitle = '', string $defaultContent = '', ?string $section = null): Content
    {
        $content = $this->findOneBy(['identifier' => $identifier]);
        
        if (!$content) {
            $content = new Content();
            $content->setIdentifier($identifier)
                   ->setTitle($defaultTitle)
                   ->setContent($defaultContent)
                   ->setSection($section);
            
            $this->getEntityManager()->persist($content);
            $this->getEntityManager()->flush();
        }
        
        return $content;
    }

    /**
     * Trouve tous les contenus d'une section spécifique
     */
    public function findBySection(string $section): array
    {
        return $this->findBy(['section' => $section], ['updatedAt' => 'DESC']);
    }
} 