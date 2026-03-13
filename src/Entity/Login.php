<?php

namespace App\Entity;

use App\Repository\LoginRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LoginRepository::class)]
class Login implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_login = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'L\'email ne peut pas être vide')]
    #[Assert\Email(message: 'L\'email doit être valide')]
    private ?string $Mail = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le mot de passe ne peut pas être vide')]
    #[Assert\Length(min: 6, minMessage: 'Le mot de passe doit faire au moins 6 caractères')]
    private ?string $Password = null;

    #[ORM\OneToMany(mappedBy: 'login', targetEntity: Utilisateur::class)]
    private Collection $utilisateurs;

    public function __construct()
    {
        $this->utilisateurs = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Utilisateur>
     */
    public function getUtilisateurs(): Collection
    {
        return $this->utilisateurs;
    }

    public function addUtilisateur(Utilisateur $utilisateur): static
    {
        if (!$this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs->add($utilisateur);
            $utilisateur->setLogin($this);
        }

        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): static
    {
        if ($this->utilisateurs->removeElement($utilisateur)) {
            if ($utilisateur->getLogin() === $this) {
                $utilisateur->setLogin(null);
            }
        }

        return $this;
    }

    // Implémentation de UserInterface
    public function getUserIdentifier(): string
    {
        return $this->Mail ?? '';
    }

    public function getRoles(): array
    {
        $roles = [];
        
        // Récupère tous les utilisateurs associés à ce login
        foreach ($this->utilisateurs as $utilisateur) {
            // Récupère tous les profils de cet utilisateur
            foreach ($utilisateur->getProfils() as $profil) {
                $role = $profil->getRole();
                if ($role && !in_array($role, $roles)) {
                    $roles[] = $role;
                }
            }
        }
        
        // Ajoute ROLE_USER par défaut
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }

        return $roles;
    }

    public function eraseCredentials(): void
    {
        // Efface les données sensibles si besoin
    }
}
