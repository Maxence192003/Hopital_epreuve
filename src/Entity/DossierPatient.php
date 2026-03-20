<?php

namespace App\Entity;

use App\Repository\DossierPatientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DossierPatientRepository::class)]
class DossierPatient
{
    #[ORM\Id]
    #[ORM\Column(length: 50)]
    private ?string $id_dossier_patient = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $Date_naissance = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $Etat_greffe = null;

    #[ORM\OneToOne(targetEntity: Utilisateur::class, inversedBy: 'dossierPatient', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'id_utilisateur', referencedColumnName: 'id_utilisateur', nullable: true)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\OneToMany(mappedBy: 'dossierPatient', targetEntity: NoteMedical::class, cascade: ['persist', 'remove'])]
    private Collection $notesMedicales;

    #[ORM\OneToMany(mappedBy: 'dossierPatient', targetEntity: Greffe::class, cascade: ['persist', 'remove'])]
    private Collection $greffes;

    public function __construct()
    {
        $this->notesMedicales = new ArrayCollection();
        $this->greffes = new ArrayCollection();
    }

    public function getIdDossierPatient(): ?string
    {
        return $this->id_dossier_patient;
    }

    public function setIdDossierPatient(string $id_dossier_patient): static
    {
        $this->id_dossier_patient = $id_dossier_patient;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->Date_naissance;
    }

    public function setDateNaissance(?\DateTimeInterface $Date_naissance): static
    {
        $this->Date_naissance = $Date_naissance;

        return $this;
    }

    public function getEtatGreffe(): ?string
    {
        return $this->Etat_greffe;
    }

    public function setEtatGreffe(?string $Etat_greffe): static
    {
        $this->Etat_greffe = $Etat_greffe;

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

    /**
     * @return Collection<int, NoteMedical>
     */
    public function getNotesMedicales(): Collection
    {
        return $this->notesMedicales;
    }

    public function addNoteMedicale(NoteMedical $noteMedicale): static
    {
        if (!$this->notesMedicales->contains($noteMedicale)) {
            $this->notesMedicales->add($noteMedicale);
            $noteMedicale->setDossierPatient($this);
        }

        return $this;
    }

    public function removeNoteMedicale(NoteMedical $noteMedicale): static
    {
        if ($this->notesMedicales->removeElement($noteMedicale)) {
            if ($noteMedicale->getDossierPatient() === $this) {
                $noteMedicale->setDossierPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Greffe>
     */
    public function getGreffes(): Collection
    {
        return $this->greffes;
    }

    public function addGreffe(Greffe $greffe): static
    {
        if (!$this->greffes->contains($greffe)) {
            $this->greffes->add($greffe);
            $greffe->setDossierPatient($this);
        }

        return $this;
    }

    public function removeGreffe(Greffe $greffe): static
    {
        if ($this->greffes->removeElement($greffe)) {
            if ($greffe->getDossierPatient() === $this) {
                $greffe->setDossierPatient(null);
            }
        }

        return $this;
    }
}
