<?php

namespace App\DataFixtures;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class NotificationFixtures extends Fixture implements DependentFixtureInterface
{
    private string $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $users = $manager->getRepository(User::class)->findAll();

        if (empty($users)) {
            return;
        }

        $notificationTypes = [
            'course_update' => [
                'Le cours "%s" a été mis à jour',
                'Un nouveau chapitre a été ajouté au cours "%s"',
                'Nouveau contenu disponible dans le cours "%s"'
            ],
            'new_message' => [
                'Vous avez reçu un message de %s',
                'Nouveau message de %s concernant le cours',
                '%s vous a envoyé un message'
            ],
            'payment_success' => [
                'Votre paiement pour le cours "%s" a été confirmé',
                'Merci pour votre achat du cours "%s"',
                'Inscription confirmée au cours "%s"'
            ],
            'quiz_result' => [
                'Vos résultats pour le quiz "%s" sont disponibles',
                'Vous avez obtenu %s%% au quiz "%s"',
                'Quiz "%s" complété avec succès'
            ],
            'course_reminder' => [
                'N\'oubliez pas de continuer le cours "%s"',
                'Reprenez votre progression dans le cours "%s"',
                'Il est temps de retourner au cours "%s"'
            ]
        ];

        foreach ($users as $user) {
            // Générer 3 à 8 notifications par utilisateur
            $numNotifications = $faker->numberBetween(3, 8);
            
            for ($i = 0; $i < $numNotifications; $i++) {
                $notification = new Notification();
                
                // Sélectionner un type de notification aléatoire
                $type = $faker->randomElement(array_keys($notificationTypes));
                $templates = $notificationTypes[$type];
                
                // Générer le contenu en fonction du type
                $content = $faker->randomElement($templates);
                switch ($type) {
                    case 'course_update':
                    case 'payment_success':
                    case 'course_reminder':
                        $content = sprintf($content, $faker->sentence(3));
                        break;
                    case 'new_message':
                        $content = sprintf($content, $faker->name);
                        break;
                    case 'quiz_result':
                        if (strpos($content, '%%') !== false) {
                            $content = sprintf($content, $faker->numberBetween(60, 100), $faker->sentence(2));
                        } else {
                            $content = sprintf($content, $faker->sentence(2));
                        }
                        break;
                }

                $notification->setContent($content)
                    ->setType($type)
                    ->setUser($user)
                    ->setCreatedAt(new \DateTimeImmutable($faker->dateTimeBetween('-1 month')->format('Y-m-d H:i:s')))
                    ->setIsRead($faker->boolean(30)); // 30% de chances d'être lue

                $manager->persist($notification);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            StudentFixtures::class,
            InstructorFixtures::class
        ];
    }
}
