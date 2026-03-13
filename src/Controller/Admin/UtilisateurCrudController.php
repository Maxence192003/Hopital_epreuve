<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use App\Entity\Login;
use App\Entity\Profil;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class UtilisateurCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Utilisateur::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // === SECTION 1 : Données civiles ===
            IdField::new('id_utilisateur')->hideOnForm(),
            TextField::new('Nom'),
            TextField::new('Prenom'),
            TextField::new('Ville_res', 'Ville'),
            TextField::new('CP', 'Code Postal'),

            // === SECTION 2 : Authentification (Login) ===
            AssociationField::new('login')
                ->setRequired(true)
                ->setHelp('Sélectionnez un compte Login'),

            // === SECTION 3 : Profils/Rôles ===
            CollectionField::new('profils')
                ->setHelp('Les rôles associés à cet utilisateur'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Gestion des Utilisateurs')
            ->setPageTitle('new', 'Créer un utilisateur')
            ->setPageTitle('edit', 'Modifier un utilisateur')
            ->setPaginatorPageSize(25)
            ->setDefaultSort(['id_utilisateur' => 'DESC']);
    }
}
