<?php

namespace App\Repository;

use App\Entity\Review;
use App\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review>
 *
 * @method Review|null find($id, $lockMode = null, $lockVersion = null)
 * @method Review|null findOneBy(array $criteria, array $orderBy = null)
 * @method Review[]    findAll()
 * @method Review[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    /**
     * Trouve toutes les reviews d'un cours
     */
    public function findByCourse($course)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.course = :course')
            ->setParameter('course', $course)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Trouve toutes les reviews d'un utilisateur
     */
    public function findByUser($user)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.user = :user')
            ->setParameter('user', $user)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Calcule la note moyenne d'un cours
     */
    public function getAverageRatingForCourse($course): ?float
    {
        $result = $this->createQueryBuilder('r')
            ->select('AVG(r.rating) as avgRating')
            ->andWhere('r.course = :course')
            ->setParameter('course', $course)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return $result ? round($result, 1) : null;
    }

    /**
     * Trouve les derniers avis
     */
    public function findLatestReviews(int $limit = 10)
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Vérifie si un utilisateur a déjà laissé un avis sur un cours
     */
    public function hasUserReviewedCourse($user, $course): bool
    {
        $review = $this->createQueryBuilder('r')
            ->andWhere('r.user = :user')
            ->andWhere('r.course = :course')
            ->setParameter('user', $user)
            ->setParameter('course', $course)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $review !== null;
    }

    public function save(Review $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Review $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function calculateAverageRating(Course $course): array
    {
        try {
            $result = $this->createQueryBuilder('r')
                ->select('COUNT(r.id) as reviewCount, ROUND(AVG(r.rating), 2) as averageRating')
                ->where('r.course = :course')
                ->andWhere('r.isApproved = :approved')
                ->setParameter('course', $course)
                ->setParameter('approved', true)
                ->getQuery()
                ->getSingleResult();

            return [
                'reviewCount' => (int)($result['reviewCount'] ?? 0),
                'averageRating' => $result['averageRating'] ? (float)$result['averageRating'] : null
            ];
        } catch (\Exception $e) {
            return [
                'reviewCount' => 0,
                'averageRating' => null
            ];
        }
    }
} 