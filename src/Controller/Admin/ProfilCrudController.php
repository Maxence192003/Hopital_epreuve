<?php

namespace App\Controller\Admin;

use App\Entity\Profil;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class ProfilCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Profil::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id_profil')->hideOnForm(),
            TextField::new('Role'),
            AssociationField::new('utilisateur')
                ->setRequired(true),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Gestion des Profils')
            ->setPageTitle('new', 'Assigner un Profil')
            ->setPageTitle('edit', 'Modifier un Profil');
    }
}
