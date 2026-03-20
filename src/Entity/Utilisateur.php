<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_utilisateur = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le nom ne peut pas être vide')]
    private ?string $Nom = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le prénom ne peut pas être vide')]
    private ?string $Prenom = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'La ville ne peut pas être vide')]
    private ?string $Ville_res = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le code postal ne peut pas être vide')]
    private ?string $CP = null;

    #[ORM\OneToOne(targetEntity: DossierPatient::class, mappedBy: 'utilisateur')]
    private ?DossierPatient $dossierPatient = null;

    #[ORM\ManyToOne(targetEntity: Login::class, inversedBy: 'utilisateurs')]
    #[ORM\JoinColumn(name: 'id_login', referencedColumnName: 'id_login', nullable: false)]
    #[Assert\NotNull(message: 'Un utilisateur doit avoir un login')]
    private ?Login $login = null;

    #[ORM\ManyToOne(targetEntity: Profil::class, inversedBy: 'utilisateurs')]
    #[ORM\JoinColumn(name: 'id_profil', referencedColumnName: 'id_profil', nullable: false)]
    #[Assert\NotNull(message: 'Un utilisateur doit avoir un profil')]
    private ?Profil $profil = null;

    public function __construct()
    {
    }

    public function getIdUtilisateur(): ?int
    {
        return $this->id_utilisateur;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(?string $Nom): static
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->Prenom;
    }

    public function setPrenom(?string $Prenom): static
    {
        $this->Prenom = $Prenom;

        return $this;
    }

    public function getVilleRes(): ?string
    {
        return $this->Ville_res;
    }

    public function setVilleRes(?string $Ville_res): static
    {
        $this->Ville_res = $Ville_res;

        return $this;
    }

    public function getCP(): ?string
    {
        return $this->CP;
    }

    public function setCP(?string $CP): static
    {
        $this->CP = $CP;

        return $this;
    }

    public function getDossierPatient(): ?DossierPatient
    {
        return $this->dossierPatient;
    }

    public function setDossierPatient(?DossierPatient $dossierPatient): static
    {
        $this->dossierPatient = $dossierPatient;

        return $this;
    }

    public function getLogin(): ?Login
    {
        return $this->login;
    }

    public function setLogin(?Login $login): static
    {
        $this->login = $login;

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
}
