<?php

namespace App\Repository;

use App\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Course>
 */
class CourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

//    /**
//     * @return Course[] Returns an array of Course objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Course
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findTrendingCourses(int $limit = 3): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.enrollments', 'e')
            ->leftJoin('c.reviews', 'r')
            ->addSelect('COUNT(e.id) as HIDDEN enrollCount')
            ->addSelect('AVG(r.rating) as HIDDEN avgRating')
            ->groupBy('c.id')
            ->orderBy('enrollCount', 'DESC')
            ->addOrderBy('avgRating', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByFilters(?string $category = null, ?float $minPrice = null, ?float $maxPrice = null): array
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.categories', 'cat')
            ->leftJoin('c.instructor', 'i')
            ->addSelect('i');

        if ($category) {
            $qb->andWhere('cat.id = :category')
               ->setParameter('category', $category);
        }

        if ($minPrice !== null) {
            $qb->andWhere('c.price >= :minPrice')
               ->setParameter('minPrice', $minPrice);
        }

        if ($maxPrice !== null) {
            $qb->andWhere('c.price <= :maxPrice')
               ->setParameter('maxPrice', $maxPrice);
        }

        return $qb->getQuery()->getResult();
    }
}
