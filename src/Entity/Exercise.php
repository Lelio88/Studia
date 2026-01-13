<?php

namespace App\Entity;

use App\Repository\ExerciseRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

use Doctrine\DBAL\Types\Types; // Add import

#[ORM\Entity(repositoryClass: ExerciseRepository::class)]
#[ApiResource]
class Exercise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $instruction = null;

    #[ORM\Column(length: 6)]
    private ?string $difficulty = null;

    #[ORM\Column]
    private ?int $expectedDurationMinutes = null;

    #[ORM\Column(nullable: true)]
    private ?array $correction = null;

    #[ORM\ManyToOne(inversedBy: 'exercises')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Session $session = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getInstruction(): ?string
    {
        return $this->instruction;
    }

    public function setInstruction(string $instruction): static
    {
        $this->instruction = $instruction;

        return $this;
    }

    public function getDifficulty(): ?string
    {
        return $this->difficulty;
    }

    public function setDifficulty(string $difficulty): static
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    public function getExpectedDurationMinutes(): ?int
    {
        return $this->expectedDurationMinutes;
    }

    public function setExpectedDurationMinutes(int $expectedDurationMinutes): static
    {
        $this->expectedDurationMinutes = $expectedDurationMinutes;

        return $this;
    }

    public function getCorrection(): ?array
    {
        return $this->correction;
    }

    public function setCorrection(?array $correction): static
    {
        $this->correction = $correction;

        return $this;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): static
    {
        $this->session = $session;

        return $this;
    }
}
