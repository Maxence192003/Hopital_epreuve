<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(private RouterInterface $router)
    {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        // Récupère l'utilisateur connecté
        $user = $token->getUser();

        // Vérifie si l'utilisateur est admin
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            // Redirige vers la page admin
            return new RedirectResponse($this->router->generate('app_admin_acceuil'));
        }

        // Par défaut, redirige vers la page d'accueil
        return new RedirectResponse($this->router->generate('app_home'));
    }
}
