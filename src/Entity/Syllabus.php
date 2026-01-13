<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\SyllabusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;



#[ORM\Entity(repositoryClass: SyllabusRepository::class)]
#[ApiResource]
class Syllabus
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $rawText = null;

    #[ORM\Column(nullable: true)]
    private ?array $extractedCompetences = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;



    #[ORM\ManyToOne(inversedBy: 'syllabi')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\OneToOne(inversedBy: 'syllabus', cascade: ['persist', 'remove'])]
    private ?CoursePlan $coursePlan = null;


    public function __construct() 
    {
        $this->createdAt = new \DateTimeImmutable();
    }

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

    public function getRawText(): ?string
    {
        return $this->rawText;
    }

    public function setRawText(string $rawText): static
    {
        $this->rawText = $rawText;

        return $this;
    }

    public function getExtractedCompetences(): ?array
    {
        return $this->extractedCompetences;
    }

    public function setExtractedCompetences(?array $extractedCompetences): static
    {
        $this->extractedCompetences = $extractedCompetences;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }



    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getCoursePlan(): ?CoursePlan
    {
        return $this->coursePlan;
    }

    public function setCoursePlan(?CoursePlan $coursePlan): static
    {
        // set the owning side of the relation if necessary
        if ($coursePlan !== null && $coursePlan->getSyllabus() !== $this) {
            $coursePlan->setSyllabus($this);
        }

        $this->coursePlan = $coursePlan;

        return $this;
    }
}
