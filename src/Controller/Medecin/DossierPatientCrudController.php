<?php

namespace App\Controller\Medecin;

use App\Entity\DossierPatient;
use App\Entity\Greffe;
use App\Entity\Utilisateur;
use App\Repository\DossierPatientRepository;
use App\Repository\UtilisateurRepository;
use App\Repository\GreffeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/medecin/dossiers')]
#[IsGranted('ROLE_MEDECIN')]
class DossierPatientCrudController extends AbstractController
{
    /**
     * SECTION 1 : Liste des dossiers patients (avec DossierPatient)
     */
    #[Route('', name: 'medecin_dossiers_index')]
    public function index(DossierPatientRepository $repository): Response
    {
        // Récupère tous les DossierPatient qui ont un utilisateur associé
        $tousLesDossiers = $repository->findAll();
        $dossiersAvecUtilisateur = [];
        
        foreach ($tousLesDossiers as $dossier) {
            if ($dossier->getUtilisateur() !== null) {
                $dossiersAvecUtilisateur[] = $dossier;
            }
        }
        
        return $this->render('medecin/dossiers/index.html.twig', [
            'dossiers' => $dossiersAvecUtilisateur,
            'section' => 'dossiers',
        ]);
    }

    /**
     * SECTION 1 : Voir les détails d'un dossier patient
     */
    #[Route('/{id}/voir', name: 'medecin_dossier_voir')]
    public function voir(DossierPatient $dossier): Response
    {
        if ($dossier->getUtilisateur() === null) {
            $this->addFlash('error', 'Ce dossier n\'a pas d\'utilisateur associé.');
            return $this->redirectToRoute('medecin_dossiers_index');
        }

        return $this->render('medecin/dossiers/voir.html.twig', [
            'dossier' => $dossier,
            'utilisateur' => $dossier->getUtilisateur(),
        ]);
    }

    /**
     * SECTION 1 : Modifier l'état de la greffe d'un dossier
     */
    #[Route('/{id}/modifier', name: 'medecin_dossier_modifier', methods: ['GET', 'POST'])]
    public function modifier(
        DossierPatient $dossier,
        Request $request,
        EntityManagerInterface $em
    ): Response
    {
        if ($dossier->getUtilisateur() === null) {
            $this->addFlash('error', 'Ce dossier n\'a pas d\'utilisateur associé.');
            return $this->redirectToRoute('medecin_dossiers_index');
        }

        if ($request->isMethod('POST')) {
            $etatGreffe = $request->request->get('etat_greffe');
            
            if (empty($etatGreffe)) {
                $this->addFlash('error', 'L\'état de la greffe est requis.');
                return $this->redirectToRoute('medecin_dossier_modifier', ['id' => $dossier->getIdDossierPatient()]);
            }

            $dossier->setEtatGreffe($etatGreffe);
            $em->flush();

            $this->addFlash('success', 'État de la greffe mis à jour avec succès.');
            return $this->redirectToRoute('medecin_dossiers_index');
        }

        return $this->render('medecin/dossiers/modifier.html.twig', [
            'dossier' => $dossier,
            'utilisateur' => $dossier->getUtilisateur(),
        ]);
    }

    /**
     * SECTION 2 : Liste des patients sans dossier
     */
    #[Route('/creer/liste', name: 'medecin_dossiers_creer_liste')]
    public function sectionCreer(UtilisateurRepository $repository, DossierPatientRepository $dossierRepo): Response
    {
        // Récupère tous les utilisateurs avec role PATIENT
        $allPatients = $repository->findByRoleName('ROLE_PATIENT');
        
        // Récupère IDs des utilisateurs qui ont déjà un dossier
        $dossiersExistants = $dossierRepo->findAll();
        $utilisateurAvecDossier = [];
        foreach ($dossiersExistants as $dossier) {
            if ($dossier->getUtilisateur() !== null) {
                $utilisateurAvecDossier[] = $dossier->getUtilisateur()->getIdUtilisateur();
            }
        }
        
        // Filtre : patients sans dossier
        $patientsSansDossier = [];
        foreach ($allPatients as $patient) {
            if (!in_array($patient->getIdUtilisateur(), $utilisateurAvecDossier)) {
                $patientsSansDossier[] = $patient;
            }
        }

        return $this->render('medecin/dossiers/creer_liste.html.twig', [
            'patients' => $patientsSansDossier,
            'section' => 'creer',
        ]);
    }

    /**
     * SECTION 2 : Créer un dossier patient pour un utilisateur
     */
    #[Route('/creer/{id}', name: 'medecin_dossier_creer', methods: ['GET', 'POST'])]
    public function creer(
        Utilisateur $utilisateur,
        Request $request,
        EntityManagerInterface $em,
        DossierPatientRepository $dossierRepo
    ): Response
    {
        // Vérifier que l'utilisateur n'a pas déjà un dossier
        $dossierExistant = $dossierRepo->findByUtilisateur($utilisateur);
        if ($dossierExistant) {
            $this->addFlash('error', 'Cet utilisateur a déjà un dossier patient.');
            return $this->redirectToRoute('medecin_dossiers_creer_liste');
        }

        if ($request->isMethod('POST')) {
            $dateNaissance = $request->request->get('date_naissance');
            $etatGreffe = $request->request->get('etat_greffe');

            // Créer le DossierPatient
            $dossier = new DossierPatient();
            $dossier->setIdDossierPatient(uniqid('DOSS_'));
            $dossier->setUtilisateur($utilisateur);
            
            if (!empty($dateNaissance)) {
                $dossier->setDateNaissance(new \DateTime($dateNaissance));
            }
            
            if (!empty($etatGreffe)) {
                $dossier->setEtatGreffe($etatGreffe);
            }

            $em->persist($dossier);
            $em->flush();

            $this->addFlash('success', 'Dossier patient créé avec succès.');
            return $this->redirectToRoute('medecin_dossiers_index');
        }

        return $this->render('medecin/dossiers/creer.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    /**
     * SECTION 3 : Liste des dossiers sans greffe
     */
    #[Route('/sans-greffe/liste', name: 'medecin_dossiers_sans_greffe')]
    public function sectionSansGreffe(DossierPatientRepository $repository): Response
    {
        $tousLesDossiers = $repository->findAll();
        $dossiersSansGreffe = [];
        
        foreach ($tousLesDossiers as $dossier) {
            if ($dossier->getUtilisateur() !== null && $dossier->getGreffes()->isEmpty()) {
                $dossiersSansGreffe[] = $dossier;
            }
        }

        return $this->render('medecin/dossiers/sans_greffe_liste.html.twig', [
            'dossiers' => $dossiersSansGreffe,
            'section' => 'sans_greffe',
        ]);
    }

    /**
     * SECTION 3 : Ajouter une greffe à un dossier
     */
    #[Route('/{id}/ajouter-greffe', name: 'medecin_dossier_ajouter_greffe', methods: ['GET', 'POST'])]
    public function ajouterGreffe(
        DossierPatient $dossier,
        Request $request,
        EntityManagerInterface $em
    ): Response
    {
        if ($dossier->getUtilisateur() === null) {
            $this->addFlash('error', 'Ce dossier n\'a pas d\'utilisateur associé.');
            return $this->redirectToRoute('medecin_dossiers_sans_greffe');
        }

        if (!$dossier->getGreffes()->isEmpty()) {
            $this->addFlash('error', 'Ce dossier a déjà une greffe.');
            return $this->redirectToRoute('medecin_dossiers_sans_greffe');
        }

        if ($request->isMethod('POST')) {
            $dateGreffe = $request->request->get('date_greffe');
            $noteGreffe = $request->request->get('note_greffe');
            $noteDonneur = $request->request->get('note_donneur');

            if (empty($dateGreffe)) {
                $this->addFlash('error', 'La date de greffe est requise.');
                return $this->redirectToRoute('medecin_dossier_ajouter_greffe', ['id' => $dossier->getIdDossierPatient()]);
            }

            $greffe = new Greffe();
            $greffe->setIdGreffe(uniqid('GREFFE_'));
            $greffe->setDossierPatient($dossier);
            $greffe->setDateGreffe(new \DateTime($dateGreffe));
            
            if (!empty($noteGreffe)) {
                $greffe->setNoteGreffe($noteGreffe);
            }
            
            if (!empty($noteDonneur)) {
                $greffe->setNoteDonneur($noteDonneur);
            }

            $em->persist($greffe);
            $em->flush();

            $this->addFlash('success', 'Greffe ajoutée avec succès.');
            return $this->redirectToRoute('medecin_dossiers_sans_greffe');
        }

        return $this->render('medecin/dossiers/ajouter_greffe.html.twig', [
            'dossier' => $dossier,
            'utilisateur' => $dossier->getUtilisateur(),
        ]);
    }
}
