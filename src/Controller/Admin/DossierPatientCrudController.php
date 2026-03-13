<?php

namespace App\Controller\Admin;

use App\Entity\DossierPatient;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;

class DossierPatientCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DossierPatient::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('nom', 'Nom du Patient'),
            TextField::new('prenom', 'Prénom'),
            DateField::new('dateNaissance', 'Date de naissance'),
            TextEditorField::new('notes', 'Notes Médicales')
                ->hideOnIndex(),
        ];
    }
}
