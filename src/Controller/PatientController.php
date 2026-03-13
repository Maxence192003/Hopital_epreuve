<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/patient')]
#[IsGranted('ROLE_PATIENT')]
class PatientController extends AbstractController
{
    #[Route('', name: 'app_patient_accueil')]
    public function accueil(): Response
    {
        $user = $this->getUser();
        $utilisateurs = $user->getUtilisateurs();
        $patient = $utilisateurs->first() ?? null;
        
        return $this->render('home/patient/accueil.html.twig', [
            'patient' => $patient,
            'user' => $user,
        ]);
    }
}
