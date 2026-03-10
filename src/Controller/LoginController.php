<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Récupère l'email de la dernière tentative (si erreur)
        $lastUsername = $authenticationUtils->getLastAuthenticationError() 
            ? '' 
            : $authenticationUtils->getLastUsername();

        // Récupère l'erreur d'authentification (si existe)
        $error = $authenticationUtils->getLastAuthenticationError();

        // Affiche le formulaire de connexion
        return $this->render('home/Login/form_login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): Response
    {
        // Cette méthode ne sera jamais exécutée car Symfony gère le logout
        // Elle doit exist mais sera interceptée par le firewall
    }
}
