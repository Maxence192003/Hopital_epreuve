<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_utilisateur = null;

    #[ORM\Column(length: 100)]
    private ?string $Nom = null;

    #[ORM\Column(length: 100)]
    private ?string $Prenom = null;

    #[ORM\Column(length: 100)]
    private ?string $Ville_res = null;

    #[ORM\Column(length: 10)]
    private ?string $CP = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'id_profil', referencedColumnName: 'id_profil', nullable: false)]
    private ?Profil $profil = null;

    #[ORM\OneToOne(mappedBy: 'utilisateur', cascade: ['persist', 'remove'])]
    private ?Login $login = null;

    public function getIdUtilisateur(): ?int
    {
        return $this->id_utilisateur;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): static
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->Prenom;
    }

    public function setPrenom(string $Prenom): static
    {
        $this->Prenom = $Prenom;

        return $this;
    }

    public function getVilleRes(): ?string
    {
        return $this->Ville_res;
    }

    public function setVilleRes(string $Ville_res): static
    {
        $this->Ville_res = $Ville_res;

        return $this;
    }

    public function getCP(): ?string
    {
        return $this->CP;
    }

    public function setCP(string $CP): static
    {
        $this->CP = $CP;

        return $this;
    }

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function setProfil(?Profil $profil): static
    {
        $this->profil = $profil;

        return $this;
    }

    public function getLogin(): ?Login
    {
        return $this->login;
    }

    public function setLogin(?Login $login): static
    {
        if ($login === null && $this->login !== null) {
            $this->login->setUtilisateur(null);
        }
        if ($login !== null && $login->getUtilisateur() !== $this) {
            $login->setUtilisateur($this);
        }

        $this->login = $login;

        return $this;
    }
}
