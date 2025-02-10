<?php

namespace App\Service;

use App\Entity\Course;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;

class ReviewService
{
    public function __construct(
        private ReviewRepository $reviewRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function updateCourseRating(Course $course): void
    {
        $stats = $this->reviewRepository->calculateAverageRating($course);
        
        if ($stats['reviewCount'] > 0) {
            $course->setReviewsCount($stats['reviewCount']);
            $course->setAverageRating($stats['averageRating']);
            
            $this->entityManager->persist($course);
            $this->entityManager->flush();
        }
    }

    public function getFormattedRating(Course $course): array
    {
        $stats = $this->reviewRepository->calculateAverageRating($course);
        
        return [
            'average' => $stats['averageRating'],
            'count' => $stats['reviewCount'],
            'stars' => $stats['averageRating'] ? round($stats['averageRating']) : 0
        ];
    }
} 