<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/evaluation', name: 'app_evaluation')]
    public function evaluation(): Response
    {
        return $this->render('home/page/evaluation.html.twig');
    }

    #[Route('/transplantation', name: 'app_transplantation')]
    public function transplantation(): Response
    {
        return $this->render('home/page/transplantation.html.twig');
    }

    #[Route('/suivi-post-greffe', name: 'app_suivi')]
    public function suivi(): Response
    {
        return $this->render('home/page/suivi.html.twig');
    }

    #[Route('/recherche', name: 'app_recherche')]
    public function recherche(): Response
    {
        return $this->render('home/page/recherche.html.twig');
    }
}
