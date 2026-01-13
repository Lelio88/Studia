<?php

namespace App\Entity;

use App\Repository\StudentProgressRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
#[ORM\Entity(repositoryClass: StudentProgressRepository::class)]
#[ApiResource]
class StudentProgress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'studentProgress')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Student $student = null;

    #[ORM\ManyToOne(inversedBy: 'studentProgress')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CoursePlan $coursePlan = null;

    #[ORM\Column(length: 12)]
    private ?string $globalLevel = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): static
    {
        $this->student = $student;

        return $this;
    }

    public function getCoursePlan(): ?CoursePlan
    {
        return $this->coursePlan;
    }

    public function setCoursePlan(?CoursePlan $coursePlan): static
    {
        $this->coursePlan = $coursePlan;

        return $this;
    }

    public function getGlobalLevel(): ?string
    {
        return $this->globalLevel;
    }

    public function setGlobalLevel(string $globalLevel): static
    {
        $this->globalLevel = $globalLevel;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }
}
