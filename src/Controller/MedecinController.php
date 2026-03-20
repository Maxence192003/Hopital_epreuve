<?php

namespace App\Controller;

use App\Entity\DossierPatient;
use App\Entity\NoteMedical;
use App\Repository\DossierPatientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/consultations', name: 'medecin_consultations')]
    public function consultations(
        DossierPatientRepository $repo,
        Request $request
    ): Response {
        // Récupérer le paramètre de recherche
        $search = $request->query->get('search', '');
        
        if ($search) {
            // Chercher par email, prénom ou nom
            $dossiers = $repo->findBySearchConsultation($search);
        } else {
            // Tous les dossiers
            $dossiers = $repo->findAll();
        }
        
        return $this->render('home/medecin/consultations/liste.html.twig', [
            'dossiers' => $dossiers,
            'search' => $search,
        ]);
    }

    #[Route('/consultations/{id}', name: 'medecin_consultation_detail')]
    public function consultationDetail(DossierPatient $dossier): Response
    {
        return $this->render('home/medecin/consultations/detail.html.twig', [
            'dossier' => $dossier,
            'notes' => $dossier->getNotesMedicales(),
        ]);
    }

    #[Route('/consultations/{id}/note', name: 'medecin_consultation_add_note', methods: ['POST'])]
    public function addNoteMedical(
        DossierPatient $dossier,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $texteNote = $request->request->get('texte_note');
        
        if (!empty($texteNote)) {
            // Créer une nouvelle note
            $note = new NoteMedical();
            $note->setIdNote(uniqid());
            $note->setTextNoteMedical($texteNote);
            // Créer la date avec le fuseau horaire Europe/Paris
            $now = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
            $note->setCreatedAt($now);
            $note->setDossierPatient($dossier);
            
            // Sauvegarder
            $em->persist($note);
            $em->flush();
            
            $this->addFlash('success', 'Note médicale ajoutée avec succès');
        }
        
        return $this->redirectToRoute('medecin_consultation_detail', [
            'id' => $dossier->getIdDossierPatient(),
        ]);
    }
}