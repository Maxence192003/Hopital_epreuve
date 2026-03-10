<?php

namespace App\Entity;

use App\Repository\LoginRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: LoginRepository::class)]
class Login implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_login = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $Mail = null;

    #[ORM\Column(length: 255)]
    private ?string $Password = null;

    #[ORM\OneToOne(inversedBy: 'login', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'id_utilisateur', referencedColumnName: 'id_utilisateur', nullable: false)]
    private ?Utilisateur $utilisateur = null;

    public function getIdLogin(): ?int
    {
        return $this->id_login;
    }

    public function getMail(): ?string
    {
        return $this->Mail;
    }

    public function setMail(string $Mail): static
    {
        $this->Mail = $Mail;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->Password;
    }

    public function setPassword(string $Password): static
    {
        $this->Password = $Password;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    // Implémentation de UserInterface
    public function getUserIdentifier(): string
    {
        return $this->Mail ?? '';
    }

    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];
        
        // Ajoute le rôle du profil si l'utilisateur existe
        if ($this->utilisateur && $this->utilisateur->getProfil()) {
            $profil = $this->utilisateur->getProfil()->getRole();
            if ($profil && !in_array($profil, $roles)) {
                $roles[] = $profil;
            }
        }

        return $roles;
    }

    public function eraseCredentials(): void
    {
        // Efface les données sensibles si besoin
    }
}
