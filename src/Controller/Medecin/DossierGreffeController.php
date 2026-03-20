<?php

namespace App\Controller\Medecin;

use App\Entity\DossierPatient;
use App\Repository\DossierPatientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/medecin/dossiers-greffe')]
#[IsGranted('ROLE_MEDECIN')]
class DossierGreffeController extends AbstractController
{
    #[Route('', name: 'medecin_dossiers_greffe_liste')]
    public function liste(DossierPatientRepository $repository): Response
    {
        // Récupère tous les DossierPatient qui ont un utilisateur associé
        $dossiers = $repository->findBy(
            ['utilisateur' => null],
            ['id_dossier_patient' => 'ASC'],
            null,
            null
        );
        
        // Récupère les dossiers qui ont un utilisateur
        $tousLesDossiers = $repository->findAll();
        $dossiersAvecUtilisateur = [];
        
        foreach ($tousLesDossiers as $dossier) {
            if ($dossier->getUtilisateur() !== null) {
                $dossiersAvecUtilisateur[] = $dossier;
            }
        }
        
        return $this->render('medecin/dossiers_greffe_liste.html.twig', [
            'dossiers' => $dossiersAvecUtilisateur,
        ]);
    }

    #[Route('/{id}/modifier', name: 'medecin_dossiers_greffe_modifier', methods: ['GET', 'POST'])]
    public function modifier(
        DossierPatient $dossier,
        Request $request,
        EntityManagerInterface $em
    ): Response
    {
        // Vérifier que le dossier a un utilisateur
        if ($dossier->getUtilisateur() === null) {
            $this->addFlash('error', 'Ce dossier n\'a pas d\'utilisateur associé.');
            return $this->redirectToRoute('medecin_dossiers_greffe_liste');
        }

        if ($request->isMethod('POST')) {
            $etatGreffe = $request->request->get('etat_greffe');
            
            // Validation simple
            if (empty($etatGreffe)) {
                $this->addFlash('error', 'L\'état de la greffe est requis.');
                return $this->redirectToRoute('medecin_dossiers_greffe_modifier', ['id' => $dossier->getIdDossierPatient()]);
            }

            // Mise à jour de l'état de la greffe
            $dossier->setEtatGreffe($etatGreffe);
            $em->flush();

            $this->addFlash('success', 'État de la greffe mis à jour avec succès.');
            return $this->redirectToRoute('medecin_dossiers_greffe_liste');
        }

        return $this->render('medecin/dossiers_greffe_modifier.html.twig', [
            'dossier' => $dossier,
            'utilisateur' => $dossier->getUtilisateur(),
        ]);
    }
}
