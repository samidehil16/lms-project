<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function __construct(private string $projectDir)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $categories = [
            [
                'name' => 'Développement Web',
                'ref' => 'category_dev_web'
            ],
            [
                'name' => 'Design',
                'ref' => 'category_design'
            ],
            [
                'name' => 'Cybersécurité',
                'ref' => 'category_security'
            ],
            [
                'name' => 'Data Science',
                'ref' => 'category_data'
            ],
            [
                'name' => 'DevOps',
                'ref' => 'category_devops'
            ]
        ];

        foreach ($categories as $categoryData) {
            $category = new Categorie();
            $category->setName($categoryData['name']);
            
            $manager->persist($category);
            $this->addReference($categoryData['ref'], $category);
        }

        $manager->flush();
    }
}
