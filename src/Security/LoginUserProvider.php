<?php

namespace App\Security;

use App\Entity\Login;
use App\Repository\LoginRepository;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class LoginUserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    public function __construct(private LoginRepository $loginRepository)
    {
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $login = $this->loginRepository->findOneByMail($identifier);

        if (!$login) {
            throw new UserNotFoundException('Utilisateur non trouvé');
        }

        return $login;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof Login) {
            throw new \InvalidArgumentException('Invalid user type');
        }

        return $this->loadUserByIdentifier($user->getMail());
    }

    public function supportsClass(string $class): bool
    {
        return $class === Login::class;
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Login) {
            throw new \InvalidArgumentException('Invalid user type');
        }

        $user->setPassword($newHashedPassword);
        $this->loginRepository->save($user, true);
    }
}
