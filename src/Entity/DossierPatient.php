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

    #[ORM\ManyToOne(targetEntity: NoteMedical::class, inversedBy: 'dossierPatients')]
    #[ORM\JoinColumn(name: 'id_note', referencedColumnName: 'id_note', nullable: false)]
    private ?NoteMedical $noteMedical = null;

    #[ORM\ManyToOne(targetEntity: Greffe::class, inversedBy: 'dossierPatients')]
    #[ORM\JoinColumn(name: 'id_greffe', referencedColumnName: 'id_greffe', nullable: true)]
    private ?Greffe $greffe = null;

    #[ORM\OneToMany(mappedBy: 'dossierPatient', targetEntity: Utilisateur::class)]
    private Collection $utilisateurs;

    public function __construct()
    {
        $this->utilisateurs = new ArrayCollection();
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

    public function getNoteMedical(): ?NoteMedical
    {
        return $this->noteMedical;
    }

    public function setNoteMedical(?NoteMedical $noteMedical): static
    {
        $this->noteMedical = $noteMedical;

        return $this;
    }

    public function getGreffe(): ?Greffe
    {
        return $this->greffe;
    }

    public function setGreffe(?Greffe $greffe): static
    {
        $this->greffe = $greffe;

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
            $utilisateur->setDossierPatient($this);
        }

        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): static
    {
        if ($this->utilisateurs->removeElement($utilisateur)) {
            if ($utilisateur->getDossierPatient() === $this) {
                $utilisateur->setDossierPatient(null);
            }
        }

        return $this;
    }
}
