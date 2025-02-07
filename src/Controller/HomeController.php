<?php

namespace App\Controller;

use App\Repository\CourseRepository;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CourseRepository $courseRepository, CategorieRepository $categoryRepository,): Response
    {
        $trendingCourses = $courseRepository->findTrendingCourses();
        // ou $trendingCourses = $courseRepository->findBy([], ['createdAt' => 'DESC'], 3);

        return $this->render('home/index.html.twig', [
            'trendingCourses' => $trendingCourses,
            'latest_courses' => $courseRepository->findBy([], ['createdAt' => 'DESC'], 6),
            'categories' => $categoryRepository->findAll(),
        ]);
    }
} 