<?php

namespace App\DataFixtures;

use App\Entity\Chapter;
use App\Entity\Course;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ChapterFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private string $projectDir)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $chaptersData = [
            'course_1' => [
                [
                    'title' => 'Introduction au développement web',
                    'content' => "Dans ce chapitre introductif, nous allons explorer les fondamentaux du développement web moderne.",
                    'video_url' => 'https://www.youtube.com/embed/8kBauwwXQpY',  // HTML/CSS pour débutants
                    'position' => 1,
                    'ref' => 'chapter_1_1'
                ],
                [
                    'title' => 'HTML5 - Structure et Sémantique',
                    'content' => "Découvrez les balises HTML5 et leur utilisation appropriée.",
                    'video_url' => 'https://www.youtube.com/embed/qsbkZ7gIKnc',  // Les bases de HTML5
                    'position' => 2,
                    'ref' => 'chapter_1_2'
                ],
                [
                    'title' => 'CSS3 - Mise en forme moderne',
                    'content' => "Maîtrisez CSS3 pour créer des mises en page responsives.",
                    'video_url' => 'https://www.youtube.com/embed/HDobHQfbRbo',  // CSS Flexbox
                    'position' => 3,
                    'ref' => 'chapter_1_3'
                ]
            ],
            'course_2' => [
                [
                    'title' => 'Les principes fondamentaux du design UX',
                    'content' => "Découvrez les principes clés qui font un bon design UX.",
                    'video_url' => 'https://www.youtube.com/embed/FRncm_Qa5Jw',  // UX Design principles
                    'position' => 1,
                    'ref' => 'chapter_2_1'
                ],
                [
                    'title' => 'Design UI - Les bases',
                    'content' => "Théorie des couleurs, typographie et composition.",
                    'video_url' => 'https://www.youtube.com/embed/zHAa-m16NGk',  // UI Design basics
                    'position' => 2,
                    'ref' => 'chapter_2_2'
                ],
                [
                    'title' => 'Figma - Prise en main',
                    'video_url' => 'https://www.youtube.com/embed/e68PKFYWfoQ',  // Figma tutorial
                    'content' => "Découverte de l'interface et des fonctionnalités de base.",
                    'position' => 3,
                    'ref' => 'chapter_2_3'
                ]
            ],
            'course_3' => [
                [
                    'title' => 'Introduction à la cybersécurité',
                    'content' => "Comprendre les bases de la sécurité informatique.",
                    'video_url' => 'https://www.youtube.com/embed/bPVaOlJ6ln0',  // Cybersecurity basics
                    'position' => 1,
                    'ref' => 'chapter_3_1'
                ],
                [
                    'title' => 'Cryptographie appliquée',
                    'content' => "Les principes de base de la cryptographie.",
                    'video_url' => 'https://www.youtube.com/embed/o_g-M7UxrI4',  // Cryptography explained
                    'position' => 2,
                    'ref' => 'chapter_3_2'
                ],
                [
                    'title' => 'Sécurité des applications Web',
                    'content' => "Protection contre les vulnérabilités courantes.",
                    'video_url' => 'https://www.youtube.com/embed/WlmKwIe9z1Q',  // Web security
                    'position' => 3,
                    'ref' => 'chapter_3_3'
                ]
            ]
        ];

        foreach ($chaptersData as $courseRef => $chapters) {
            foreach ($chapters as $chapterData) {
                $chapter = new Chapter();
                $chapter->setTitle($chapterData['title'])
                    ->setContent($chapterData['content'])
                    ->setVideoUrl($chapterData['video_url'])
                    ->setPosition($chapterData['position'])
                    ->setCourse($this->getReference($courseRef, Course::class));
                
                $manager->persist($chapter);
                $this->addReference($chapterData['ref'], $chapter);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CourseFixtures::class,
        ];
    }
}
