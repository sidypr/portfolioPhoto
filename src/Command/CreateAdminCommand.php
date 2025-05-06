<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Créer un nouvel utilisateur administrateur',
)]
class CreateAdminCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'Email de l\'administrateur')
            ->addArgument('password', InputArgument::OPTIONAL, 'Mot de passe de l\'administrateur')
            ->addArgument('fullname', InputArgument::OPTIONAL, 'Nom complet de l\'administrateur')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');

        // Récupérer les arguments ou demander interactivement
        $email = $input->getArgument('email');
        if (!$email) {
            $question = new Question('Email de l\'administrateur: ');
            $email = $helper->ask($input, $output, $question);
        }

        $password = $input->getArgument('password');
        if (!$password) {
            $question = new Question('Mot de passe: ');
            $question->setHidden(true);
            $question->setHiddenFallback(false);
            $password = $helper->ask($input, $output, $question);
        }

        $fullName = $input->getArgument('fullname');
        if (!$fullName) {
            $question = new Question('Nom complet: ');
            $fullName = $helper->ask($input, $output, $question);
        }

        // Vérifier si l'utilisateur existe déjà
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            $io->error(sprintf('Un utilisateur avec l\'email "%s" existe déjà', $email));
            return Command::FAILURE;
        }

        // Créer l'utilisateur administrateur
        $user = new User();
        $user->setEmail($email);
        $user->setFullName($fullName);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $password
            )
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(sprintf('Administrateur créé avec succès: %s', $email));

        return Command::SUCCESS;
    }
} 