<?php

namespace App\Command;

use App\Entity\Login;
use App\Entity\Utilisateur;
use App\Entity\Profil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;

#[AsCommand(name: 'app:create-admin')]
final class CreateAdminCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Hash the password
        $hasher = password_hash('password123', PASSWORD_BCRYPT, ['cost' => 12]);
        
        // Create Login
        $login = new Login();
        $login->setMail('admin@hopital.fr');
        $login->setPassword($hasher);
        
        // Create or get Profil first
        $profil = $this->entityManager->getRepository(Profil::class)
            ->findOneBy(['Role' => 'ROLE_ADMIN']);
        
        if (!$profil) {
            $profil = new Profil();
            $profil->setRole('ROLE_ADMIN');
            $this->entityManager->persist($profil);
            $this->entityManager->flush();
        }
        
        // Create Utilisateur with Profil
        $utilisateur = new Utilisateur();
        $utilisateur->setNom('Admin');
        $utilisateur->setPrenom('Admin');
        $utilisateur->setVilleRes('Limoges');
        $utilisateur->setCP('87000');
        $utilisateur->setLogin($login);
        $utilisateur->setProfil($profil);
        $login->addUtilisateur($utilisateur);
        
        // Persist
        $this->entityManager->persist($login);
        $this->entityManager->persist($utilisateur);
        $this->entityManager->flush();
        
        $output->writeln('Admin user created successfully!');
        $output->writeln('Email: admin@hopital.fr');
        $output->writeln('Password: password123');
        $output->writeln('Password hash: ' . $hasher);
        
        return Command::SUCCESS;
    }
}
