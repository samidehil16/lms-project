<?php

namespace App\Repository;

use App\Entity\Enrollment;
use App\Entity\Student;
use App\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Enrollment>
 *
 * @method Enrollment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Enrollment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Enrollment[]    findAll()
 * @method Enrollment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnrollmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Enrollment::class);
    }

    /**
     * Trouve tous les enrollments d'un étudiant spécifique
     */
    public function findByStudent(Student $student)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.student = :student')
            ->setParameter('student', $student)
            ->orderBy('e.enrolledAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Trouve tous les enrollments pour un cours spécifique
     */
    public function findByCourse(Course $course)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.course = :course')
            ->setParameter('course', $course)
            ->orderBy('e.enrolledAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Vérifie si un étudiant est inscrit à un cours
     */
    public function isEnrolled(Student $student, Course $course): bool
    {
        $enrollment = $this->createQueryBuilder('e')
            ->andWhere('e.student = :student')
            ->andWhere('e.course = :course')
            ->setParameter('student', $student)
            ->setParameter('course', $course)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $enrollment !== null;
    }

    /**
     * Compte le nombre d'inscrits pour un cours
     */
    public function countEnrollmentsByCourse(Course $course): int
    {
        return $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->andWhere('e.course = :course')
            ->setParameter('course', $course)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * Trouve les dernières inscriptions
     */
    public function findLatestEnrollments(int $limit = 10)
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.enrolledAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function save(Enrollment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Enrollment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
} 