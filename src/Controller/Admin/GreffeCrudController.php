<?php

namespace App\Controller\Admin;

use App\Entity\Greffe;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;

class GreffeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Greffe::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('typeGreffe', 'Type de Greffe'),
            DateField::new('dateGreffe', 'Date de la Greffe'),
            TextField::new('statut', 'Statut'),
            TextEditorField::new('notes', 'Notes')
                ->hideOnIndex(),
        ];
    }
}
