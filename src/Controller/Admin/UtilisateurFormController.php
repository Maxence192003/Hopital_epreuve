<?php

namespace App\Controller\Admin;

use App\Entity\Login;
use App\Entity\Utilisateur;
use App\Entity\Profil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class UtilisateurFormController extends AbstractController
{
    #[Route('/admin/utilisateur-liste', name: 'admin_utilisateur_liste', methods: ['GET'])]
    public function liste(EntityManagerInterface $em): Response
    {
        $utilisateurs = $em->getRepository(Utilisateur::class)->findAll();
        return $this->render('admin/utilisateur_list.html.twig', [
            'utilisateurs' => $utilisateurs
        ]);
    }

    #[Route('/admin/utilisateur', name: 'admin_utilisateur_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $utilisateurs = $em->getRepository(Utilisateur::class)->findAll();
        return $this->render('admin/utilisateur_list.html.twig', [
            'utilisateurs' => $utilisateurs
        ]);
    }

    #[Route('/admin/utilisateur/creer', name: 'admin_utilisateur_creer', methods: ['GET', 'POST'])]
    public function creer(
        Request $request,
        EntityManagerInterface $em,
        PasswordHasherFactoryInterface $passwordHasherFactory
    ): Response {
        if ($request->isMethod('POST')) {
            // Récupérer les données du formulaire
            $data = $request->request->all();

            // Créer le Login
            $login = new Login();
            $login->setMail($data['mail']);
            $hasher = $passwordHasherFactory->getPasswordHasher(Login::class);
            $login->setPassword($hasher->hash($data['password']));
            $em->persist($login);
            $em->flush();

            // Créer ou récupérer le Profil
            $profil = $em->getRepository(Profil::class)->findOneBy(['Role' => $data['role']]);
            if (!$profil) {
                $profil = new Profil();
                $profil->setRole($data['role']);
                $em->persist($profil);
                $em->flush();
            }

            // Créer l'Utilisateur avec le Profil
            $utilisateur = new Utilisateur();
            $utilisateur->setNom($data['nom']);
            $utilisateur->setPrenom($data['prenom']);
            $utilisateur->setVilleRes($data['ville_res']);
            $utilisateur->setCP($data['cp']);
            $utilisateur->setLogin($login);
            $utilisateur->setProfil($profil);
            $em->persist($utilisateur);
            $em->flush();

            $this->addFlash('success', 'Utilisateur créé avec succès !');
            return $this->redirectToRoute('admin_utilisateur_liste');
        }

        return $this->render('admin/utilisateur_form.html.twig');
    }

    #[Route('/admin/utilisateur/{id}/voir', name: 'admin_utilisateur_voir', methods: ['GET'])]
    public function voir(Utilisateur $utilisateur): Response
    {
        return $this->render('admin/utilisateur_voir.html.twig', [
            'utilisateur' => $utilisateur
        ]);
    }

    #[Route('/admin/utilisateur/{id}/modifier', name: 'admin_utilisateur_modifier', methods: ['GET', 'POST'])]
    public function modifier(
        Utilisateur $utilisateur,
        Request $request,
        EntityManagerInterface $em,
        PasswordHasherFactoryInterface $passwordHasherFactory
    ): Response {
        if ($request->isMethod('POST')) {
            $data = $request->request->all();

            // Modifier l'Utilisateur
            $utilisateur->setNom($data['nom']);
            $utilisateur->setPrenom($data['prenom']);
            $utilisateur->setVilleRes($data['ville_res']);
            $utilisateur->setCP($data['cp']);

            // Modifier le Profil
            if ($utilisateur->getProfil()) {
                $utilisateur->getProfil()->setRole($data['role']);
            }

            // Modifier le Login si email ou password ont changé
            if ($utilisateur->getLogin()->getMail() !== $data['mail']) {
                $utilisateur->getLogin()->setMail($data['mail']);
            }
            if (!empty($data['password']) && $data['password'] !== '') {
                $hasher = $passwordHasherFactory->getPasswordHasher(Login::class);
                $utilisateur->getLogin()->setPassword($hasher->hash($data['password']));
            }

            $em->flush();

            $this->addFlash('success', 'Utilisateur modifié avec succès !');
            return $this->redirectToRoute('admin_utilisateur_liste');
        }

        return $this->render('admin/utilisateur_modifier.html.twig', [
            'utilisateur' => $utilisateur
        ]);
    }

    #[Route('/admin/utilisateur/{id}/supprimer', name: 'admin_utilisateur_supprimer', methods: ['POST'])]
    public function supprimer(
        Utilisateur $utilisateur,
        EntityManagerInterface $em
    ): Response {
        $em->remove($utilisateur);
        $em->flush();

        $this->addFlash('success', 'Utilisateur supprimé avec succès !');
        return $this->redirectToRoute('admin_utilisateur_liste');
    }
}
