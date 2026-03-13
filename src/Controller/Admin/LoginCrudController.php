<?php

namespace App\Controller\Admin;

use App\Entity\Login;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class LoginCrudController extends AbstractCrudController
{
    public function __construct(private PasswordHasherFactoryInterface $passwordHasherFactory) {}

    public static function getEntityFqcn(): string
    {
        return Login::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id_login')->hideOnForm(),
            EmailField::new('Mail', 'Email')
                ->setRequired(true),
            TextField::new('Password', 'Mot de passe')
                ->setRequired(true)
                ->hideOnIndex(),  // Ne pas montrer en liste
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Gestion des Logins')
            ->setPageTitle('new', 'Créer un Login')
            ->setPageTitle('edit', 'Modifier un Login');
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Login) {
            // Hasher le mot de passe avant de sauvegarder
            $hasher = $this->passwordHasherFactory->getPasswordHasher(Login::class);
            $hashedPassword = $hasher->hash($entityInstance->getPassword());
            $entityInstance->setPassword($hashedPassword);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Login) {
            // Si le mot de passe a changé, le hasher
            if ($entityInstance->getPassword() && !str_starts_with($entityInstance->getPassword(), '$2')) {
                $hasher = $this->passwordHasherFactory->getPasswordHasher(Login::class);
                $hashedPassword = $hasher->hash($entityInstance->getPassword());
                $entityInstance->setPassword($hashedPassword);
            }
        }

        parent::updateEntity($entityManager, $entityInstance);
    }
}
