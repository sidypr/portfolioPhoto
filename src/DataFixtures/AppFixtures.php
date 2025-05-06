<?php

namespace App\DataFixtures;

use App\Entity\Photo;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Créer l'utilisateur administrateur
        $admin = new User();
        $admin->setEmail('admin@portfolio.com');
        $admin->setFullName('Administrateur');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword(
            $this->passwordHasher->hashPassword(
                $admin,
                'admin123'
            )
        );

        $manager->persist($admin);
        
        // Créer quelques photos
        $categories = ['Portrait', 'Paysage', 'Architecture', 'Nature', 'Événement', 'Sport'];
        
        for ($i = 1; $i <= 10; $i++) {
            $photo = new Photo();
            $photo->setTitle('Photo de test ' . $i);
            $photo->setCategory($categories[array_rand($categories)]);
            $photo->setFilename('default.jpg');
            
            $manager->persist($photo);
        }

        $manager->flush();
    }
} 