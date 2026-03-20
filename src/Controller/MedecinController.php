<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/medecin')]
#[IsGranted('ROLE_MEDECIN')]
class MedecinController extends AbstractController
{
    #[Route('', name: 'app_medecin_accueil')]
    public function accueil(): Response
    {
        $user = $this->getUser();
        
        // $user est un Login avec une OneToMany vers Utilisateur
        $utilisateurs = $user->getUtilisateurs();
        
        // Récupère le premier utilisateur associé au login
        $medecin = null;
        if (!$utilisateurs->isEmpty()) {
            $medecin = $utilisateurs->first();
        }
        
        return $this->render('home/medecin/accueil.html.twig', [
            'medecin' => $medecin,
            'user' => $user,
        ]);
    }
}