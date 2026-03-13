<?php

namespace App\Entity;

use App\Repository\ProfilRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProfilRepository::class)]
class Profil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_profil = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le rôle ne peut pas être vide')]
    private ?string $Role = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'profils')]
    #[ORM\JoinColumn(name: 'id_utilisateur', referencedColumnName: 'id_utilisateur', nullable: false)]
    #[Assert\NotNull(message: 'Un profil doit être associé à un utilisateur')]
    private ?Utilisateur $utilisateur = null;

    public function getIdProfil(): ?int
    {
        return $this->id_profil;
    }

    public function getRole(): ?string
    {
        return $this->Role;
    }

    public function setRole(string $Role): static
    {
        $this->Role = $Role;

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
}
