<?php

namespace App\Entity;

use App\Repository\EnrollmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnrollmentRepository::class)]
class Enrollment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'enrollments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Course $course = null;

    #[ORM\ManyToOne(inversedBy: 'enrollments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Student $student = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $enrolledAt = null;

    #[ORM\Column]
    private ?int $progressPercentage = 0;

    #[ORM\Column(type: 'json')]
    private array $completedChapters = [];

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $lastAccessAt = null;

    public function __construct()
    {
        $this->enrolledAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): static
    {
        $this->course = $course;

        return $this;
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

    public function getEnrolledAt(): ?\DateTimeImmutable
    {
        return $this->enrolledAt;
    }

    public function setEnrolledAt(\DateTimeImmutable $enrolledAt): static
    {
        $this->enrolledAt = $enrolledAt;

        return $this;
    }

    public function getProgressPercentage(): ?int
    {
        return $this->progressPercentage;
    }

    public function setProgressPercentage(int $progressPercentage): self
    {
        $this->progressPercentage = $progressPercentage;
        return $this;
    }

    public function getCompletedChapters(): array
    {
        return $this->completedChapters;
    }

    public function setCompletedChapters(array $completedChapters): self
    {
        $this->completedChapters = $completedChapters;
        return $this;
    }

    public function addCompletedChapter(int $chapterId): self
    {
        if (!in_array($chapterId, $this->completedChapters)) {
            $this->completedChapters[] = $chapterId;
            $this->updateProgressPercentage();
        }
        return $this;
    }

    public function getLastAccessAt(): ?\DateTimeImmutable
    {
        return $this->lastAccessAt;
    }

    public function setLastAccessAt(?\DateTimeImmutable $lastAccessAt): self
    {
        $this->lastAccessAt = $lastAccessAt;
        return $this;
    }

    private function updateProgressPercentage(): void
    {
        if ($this->course) {
            $totalChapters = count($this->course->getChapters());
            if ($totalChapters > 0) {
                $this->progressPercentage = (int)(count($this->completedChapters) / $totalChapters * 100);
            }
        }
    }
}