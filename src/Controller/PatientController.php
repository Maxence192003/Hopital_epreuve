<?php

namespace App\Controller;

use App\Repository\DossierPatientRepository;
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

    #[Route('/dossier', name: 'app_patient_dossier')]
    public function dossier(DossierPatientRepository $dossierRepository): Response
    {
        $user = $this->getUser();
        $utilisateurs = $user->getUtilisateurs();
        $patient = $utilisateurs->first() ?? null;

        if (!$patient) {
            throw $this->createNotFoundException('Patient introuvable.');
        }

        $dossier = $dossierRepository->findByUtilisateur($patient);

        if (!$dossier) {
            throw $this->createNotFoundException('Aucun dossier medical n\'est associe a ce patient.');
        }

        return $this->render('home/patient/dossier.html.twig', [
            'patient' => $patient,
            'dossier' => $dossier,
            'notes' => $dossier->getNotesMedicales(),
            'greffes' => $dossier->getGreffes(),
        ]);
    }
}
