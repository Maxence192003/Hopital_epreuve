<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use App\Entity\DossierPatient;
use App\Entity\Greffe;
use App\Entity\Login;
use App\Entity\Profil;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator) {}

    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Institut de Greffe - Admin')
            ->setFaviconPath('images/favicon.svg');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');
        yield MenuItem::linkToRoute('Voir Utilisateurs', 'fas fa-users', 'admin_utilisateur_liste');
        yield MenuItem::linkToRoute('Ajouter Utilisateur', 'fas fa-user-plus', 'admin_utilisateur_creer');
    }
}