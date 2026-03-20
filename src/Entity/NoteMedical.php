<?php

namespace App\Entity;

use App\Repository\NoteMedicalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NoteMedicalRepository::class)]
class NoteMedical
{
    #[ORM\Id]
    #[ORM\Column(length: 50)]
    private ?string $id_note = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $Text_note_medical = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\ManyToOne(targetEntity: DossierPatient::class, inversedBy: 'notesMedicales')]
    #[ORM\JoinColumn(name: 'id_dossier_patient', referencedColumnName: 'id_dossier_patient', nullable: false)]
    private ?DossierPatient $dossierPatient = null;

    public function __construct()
    {
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

    public function getDossierPatient(): ?DossierPatient
    {
        return $this->dossierPatient;
    }

    public function setDossierPatient(?DossierPatient $dossierPatient): static
    {
        $this->dossierPatient = $dossierPatient;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }
}
