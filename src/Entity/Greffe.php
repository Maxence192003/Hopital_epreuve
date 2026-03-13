<?php

namespace App\Entity;

use App\Repository\GreffeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GreffeRepository::class)]
class Greffe
{
    #[ORM\Id]
    #[ORM\Column(length: 50)]
    private ?string $id_greffe = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $Date_greffe = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $Note_greffe = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $Note_donneur = null;

    #[ORM\OneToMany(mappedBy: 'greffe', targetEntity: DossierPatient::class)]
    private Collection $dossierPatients;

    public function __construct()
    {
        $this->dossierPatients = new ArrayCollection();
    }

    public function getIdGreffe(): ?string
    {
        return $this->id_greffe;
    }

    public function setIdGreffe(string $id_greffe): static
    {
        $this->id_greffe = $id_greffe;

        return $this;
    }

    public function getDateGreffe(): ?\DateTimeInterface
    {
        return $this->Date_greffe;
    }

    public function setDateGreffe(?\DateTimeInterface $Date_greffe): static
    {
        $this->Date_greffe = $Date_greffe;

        return $this;
    }

    public function getNoteGreffe(): ?string
    {
        return $this->Note_greffe;
    }

    public function setNoteGreffe(?string $Note_greffe): static
    {
        $this->Note_greffe = $Note_greffe;

        return $this;
    }

    public function getNoteDonneur(): ?string
    {
        return $this->Note_donneur;
    }

    public function setNoteDonneur(?string $Note_donneur): static
    {
        $this->Note_donneur = $Note_donneur;

        return $this;
    }

    /**
     * @return Collection<int, DossierPatient>
     */
    public function getDossierPatients(): Collection
    {
        return $this->dossierPatients;
    }

    public function addDossierPatient(DossierPatient $dossierPatient): static
    {
        if (!$this->dossierPatients->contains($dossierPatient)) {
            $this->dossierPatients->add($dossierPatient);
            $dossierPatient->setGreffe($this);
        }

        return $this;
    }

    public function removeDossierPatient(DossierPatient $dossierPatient): static
    {
        if ($this->dossierPatients->removeElement($dossierPatient)) {
            if ($dossierPatient->getGreffe() === $this) {
                $dossierPatient->setGreffe(null);
            }
        }

        return $this;
    }
}
