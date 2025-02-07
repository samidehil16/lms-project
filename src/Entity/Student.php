<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
class Student extends User
{
    
    #[ORM\Column]
    
    private array $progress = [];

    /**
     * @var Collection<int, Payment>
     */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'student')]
    private Collection $payments;

    /**
     * @var Collection<int, Enrollment>
     */
    #[ORM\OneToMany(mappedBy: 'student', targetEntity: Enrollment::class)]
    private Collection $enrollments;

    public function __construct()
    {
        parent::__construct();
        $this->payments = new ArrayCollection();
        $this->enrollments = new ArrayCollection();
    }

    public function getProgress(): array
    {
        return $this->progress;
    }

    public function setProgress(array $progress): static
    {
        $this->progress = $progress;

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setStudent($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getStudent() === $this) {
                $payment->setStudent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Enrollment>
     */
    public function getEnrollments(): Collection
    {
        return $this->enrollments;
    }

    public function addEnrollment(Enrollment $enrollment): static
    {
        if (!$this->enrollments->contains($enrollment)) {
            $this->enrollments->add($enrollment);
            $enrollment->setStudent($this);
        }

        return $this;
    }

    public function removeEnrollment(Enrollment $enrollment): static
    {
        if ($this->enrollments->removeElement($enrollment)) {
            // set the owning side to null (unless already changed)
            if ($enrollment->getStudent() === $this) {
                $enrollment->setStudent(null);
            }
        }

        return $this;
    }
}
