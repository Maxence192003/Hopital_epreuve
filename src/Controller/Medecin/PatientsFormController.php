<?php

namespace App\Controller\Medecin;

use App\Entity\Login;
use App\Entity\Profil;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use PasswordHasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_MEDECIN')]
#[Route('/medecin/patients')]
class PatientsFormController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PasswordHasherFactoryInterface $hasherFactory
    ) {}

    /**
     * List all patients (users with ROLE_PATIENT)
     */
    #[Route('/liste', name: 'medecin_patients_liste', methods: ['GET'])]
    public function liste(): Response
    {
        $patients = $this->entityManager->getRepository(Utilisateur::class)
            ->createQueryBuilder('u')
            ->leftJoin('u.profil', 'p')
            ->where('p.Role = :role')
            ->setParameter('role', 'ROLE_PATIENT')
            ->orderBy('u.Nom', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('medecin/patients_list.html.twig', [
            'patients' => $patients
        ]);
    }

    /**
     * View patient details
     */
    #[Route('/{id}/voir', name: 'medecin_patients_voir', methods: ['GET'])]
    public function voir($id): Response
    {
        $patient = $this->entityManager->getRepository(Utilisateur::class)->find($id);

        if (!$patient) {
            throw $this->createNotFoundException('Patient introuvable');
        }

        // Vérifier que c'est bien un patient
        $profil = $patient->getProfil();
        if (!$profil || $profil->getRole() !== 'ROLE_PATIENT') {
            throw $this->createAccessDeniedException('Accès refusé');
        }

        return $this->render('medecin/patients_voir.html.twig', [
            'patient' => $patient
        ]);
    }

    /**
     * Create new patient
     */
    #[Route('/creer', name: 'medecin_patients_creer', methods: ['GET', 'POST'])]
    public function creer(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $nom = $request->request->get('nom');
            $prenom = $request->request->get('prenom');
            $ville_res = $request->request->get('ville_res');
            $cp = $request->request->get('cp');
            $mail = $request->request->get('mail');
            $password = $request->request->get('password');

            // Create Login
            $login = new Login();
            $login->setMail($mail);

            // Hash password
            $hashedPassword = $this->hasherFactory
                ->getPasswordHasher(Login::class)
                ->hash($password);
            $login->setPassword($hashedPassword);

            $this->entityManager->persist($login);
            $this->entityManager->flush();

            // Get or create Profil (ROLE_PATIENT only) BEFORE creating Utilisateur
            $profil = $this->entityManager->getRepository(Profil::class)
                ->findOneBy(['Role' => 'ROLE_PATIENT']);
            
            if (!$profil) {
                $profil = new Profil();
                $profil->setRole('ROLE_PATIENT');
                $this->entityManager->persist($profil);
                $this->entityManager->flush();
            }

            // Create Utilisateur with Profil already set
            $utilisateur = new Utilisateur();
            $utilisateur->setNom($nom);
            $utilisateur->setPrenom($prenom);
            $utilisateur->setVilleRes($ville_res);
            $utilisateur->setCP($cp);
            $utilisateur->setLogin($login);
            $utilisateur->setProfil($profil);

            $this->entityManager->persist($utilisateur);
            $this->entityManager->flush();

            return $this->redirectToRoute('medecin_patients_liste');
        }

        return $this->render('medecin/patients_form.html.twig', [
            'title' => 'Ajouter un patient'
        ]);
    }

    /**
     * Edit patient
     */
    #[Route('/{id}/modifier', name: 'medecin_patients_modifier', methods: ['GET', 'POST'])]
    public function modifier($id, Request $request): Response
    {
        $patient = $this->entityManager->getRepository(Utilisateur::class)->find($id);

        if (!$patient) {
            throw $this->createNotFoundException('Patient introuvable');
        }

        // Vérifier que c'est bien un patient
        $profil = $patient->getProfil();
        if (!$profil || $profil->getRole() !== 'ROLE_PATIENT') {
            throw $this->createAccessDeniedException('Accès refusé');
        }

        if ($request->isMethod('POST')) {
            // Update Utilisateur
            $patient->setNom($request->request->get('nom'));
            $patient->setPrenom($request->request->get('prenom'));
            $patient->setVilleRes($request->request->get('ville_res'));
            $patient->setCP($request->request->get('cp'));

            // Update Login
            $login = $patient->getLogin();
            if ($login) {
                $login->setMail($request->request->get('mail'));

                // Update password if provided
                $password = $request->request->get('password');
                if ($password) {
                    // Check if it's already hashed (bcrypt hashes start with $2)
                    if (!str_starts_with($password, '$2')) {
                        $hashedPassword = $this->hasherFactory
                            ->getPasswordHasher(Login::class)
                            ->hash($password);
                        $login->setPassword($hashedPassword);
                    }
                }
            }

            $this->entityManager->flush();

            return $this->redirectToRoute('medecin_patients_liste');
        }

        return $this->render('medecin/patients_modifier.html.twig', [
            'patient' => $patient
        ]);
    }

    /**
     * Delete patient
     */
    #[Route('/{id}/supprimer', name: 'medecin_patients_supprimer', methods: ['POST'])]
    public function supprimer($id, Request $request): Response
    {
        $patient = $this->entityManager->getRepository(Utilisateur::class)->find($id);

        if (!$patient) {
            throw $this->createNotFoundException('Patient introuvable');
        }

        // Vérifier que c'est bien un patient
        $profil = $patient->getProfil();
        if (!$profil || $profil->getRole() !== 'ROLE_PATIENT') {
            throw $this->createAccessDeniedException('Accès refusé');
        }

        // Delete associated login
        $login = $patient->getLogin();
        if ($login) {
            $this->entityManager->remove($login);
        }

        // Delete patient (cascade will remove profils)
        $this->entityManager->remove($patient);
        $this->entityManager->flush();

        return $this->redirectToRoute('medecin_patients_liste');
    }
}
