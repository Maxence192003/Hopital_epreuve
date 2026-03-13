<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class AdminController extends AbstractController
{
    #[Route('/admin/dashboard/acceuil', name: 'app_admin_acceuil')]
    public function acceuil(): Response
    {
        return $this->render('home/admin/acceuil.html.twig');
    }
}
