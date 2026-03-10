<?php

namespace App\Entity;

use App\Repository\ProfilRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfilRepository::class)]
class Profil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_profil = null;

    #[ORM\Column(length: 50)]
    private ?string $Role = null;

    #[ORM\OneToOne(mappedBy: 'profil', cascade: ['persist', 'remove'])]
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
        if ($utilisateur === null && $this->utilisateur !== null) {
            $this->utilisateur->setProfil(null);
        }
        if ($utilisateur !== null && $utilisateur->getProfil() !== $this) {
            $utilisateur->setProfil($this);
        }

        $this->utilisateur = $utilisateur;

        return $this;
    }
}
