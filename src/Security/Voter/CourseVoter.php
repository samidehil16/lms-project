<?php

namespace App\Security\Voter;

use App\Entity\Course;
use App\Entity\Student;
use App\Entity\Instructor;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class CourseVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';
    const ACCESS_CONTENT = 'access_content';

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Course && in_array($attribute, [self::VIEW, self::EDIT, self::DELETE]);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // Pour la visualisation, permettre l'accès public
        if ($attribute === self::VIEW) {
            return true;
        }

        // Pour les autres actions, vérifier l'utilisateur
        if (!$user instanceof User) {
            return false;
        }

        /** @var Course $course */
        $course = $subject;

        return match($attribute) {
            self::EDIT => $this->canEdit($course, $user),
            self::DELETE => $this->canDelete($course, $user),
            default => false,
        };
    }

    private function canEdit(Course $course, User $user): bool
    {
        // L'instructeur du cours peut l'éditer
        if ($course->getInstructor() === $user) {
            return true;
        }

        // Les admins peuvent éditer tous les cours
        return in_array('ROLE_ADMIN', $user->getRoles());
    }

    private function canDelete(Course $course, User $user): bool
    {
        // Seuls les admins peuvent supprimer des cours
        return in_array('ROLE_ADMIN', $user->getRoles());
    }

    private function canAccessContent(Course $course, UserInterface $user): bool
    {
        // Si c'est l'instructeur du cours
        if ($user instanceof Instructor && $course->getInstructor() === $user) {
            return true;
        }

        // Si c'est un étudiant inscrit au cours
        if ($user instanceof Student) {
            $enrollment = $this->entityManager->getRepository('App\Entity\Enrollment')->findOneBy([
                'student' => $user,
                'course' => $course
            ]);

            return $enrollment !== null;
        }

        return false;
    }
} 