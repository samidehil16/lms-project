<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    private string $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function load(ObjectManager $manager): void
    {
        $categories = [
            'Développement Web' => [
                'Symfony', 'React', 'Vue.js', 'Laravel', 'Node.js'
            ],
            'Design' => [
                'UI/UX', 'Figma', 'Adobe XD', 'Photoshop'
            ],
            'Marketing Digital' => [
                'SEO', 'Réseaux Sociaux', 'Google Ads', 'Content Marketing'
            ],
            'Data Science' => [
                'Python', 'Machine Learning', 'Deep Learning', 'Big Data'
            ],
            'DevOps' => [
                'Docker', 'Kubernetes', 'CI/CD', 'AWS', 'Azure'
            ]
        ];

        foreach ($categories as $mainCategory => $subCategories) {
            $category = new Categorie();
            $category->setName($mainCategory);
            $manager->persist($category);
            
            // On stocke la référence pour CourseFixtures
            $this->addReference('category_' . strtolower(str_replace(' ', '_', $mainCategory)), $category);
        }

        $manager->flush();
    }
}
