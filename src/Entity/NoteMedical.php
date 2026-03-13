<?php

namespace App\Entity;

use App\Repository\NoteMedicalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NoteMedicalRepository::class)]
class NoteMedical
{
    #[ORM\Id]
    #[ORM\Column(length: 50)]
    private ?string $id_note = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $Text_note_medical = null;

    #[ORM\OneToMany(mappedBy: 'noteMedical', targetEntity: DossierPatient::class)]
    private Collection $dossierPatients;

    public function __construct()
    {
        $this->dossierPatients = new ArrayCollection();
    }

    public function getIdNote(): ?string
    {
        return $this->id_note;
    }

    public function setIdNote(string $id_note): static
    {
        $this->id_note = $id_note;

        return $this;
    }

    public function getTextNoteMedical(): ?string
    {
        return $this->Text_note_medical;
    }

    public function setTextNoteMedical(?string $Text_note_medical): static
    {
        $this->Text_note_medical = $Text_note_medical;

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
            $dossierPatient->setNoteMedical($this);
        }

        return $this;
    }

    public function removeDossierPatient(DossierPatient $dossierPatient): static
    {
        if ($this->dossierPatients->removeElement($dossierPatient)) {
            if ($dossierPatient->getNoteMedical() === $this) {
                $dossierPatient->setNoteMedical(null);
            }
        }

        return $this;
    }
}
