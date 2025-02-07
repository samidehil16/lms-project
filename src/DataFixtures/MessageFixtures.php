<?php

namespace App\DataFixtures;

use App\Entity\Message;
use App\Entity\Student;
use App\Entity\Instructor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class MessageFixtures extends Fixture implements DependentFixtureInterface
{
    private string $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $students = $manager->getRepository(Student::class)->findAll();
        $instructors = $manager->getRepository(Instructor::class)->findAll();

        if (empty($students) || empty($instructors)) {
            return;
        }

        // Messages des étudiants vers les instructeurs
        foreach ($students as $student) {
            // Chaque étudiant envoie 1 à 3 messages à des instructeurs aléatoires
            $numMessages = $faker->numberBetween(1, 3);
            
            for ($i = 0; $i < $numMessages; $i++) {
                $message = new Message();
                
                // Création d'un contenu plus réaliste
                $content = $faker->randomElement([
                    "Bonjour,\n\nJ'ai une question concernant le chapitre sur %s. Pourriez-vous m'éclairer ?\n\nCordialement,\n%s",
                    "Bonjour,\n\nJe n'arrive pas à comprendre la partie sur %s. Auriez-vous des ressources supplémentaires ?\n\nMerci d'avance,\n%s",
                    "Bonjour,\n\nJe bloque sur l'exercice concernant %s. Pouvez-vous m'aider ?\n\nMerci,\n%s"
                ]);

                $message->setContent(sprintf(
                    $content,
                    $faker->randomElement(['les frameworks', 'la POO', 'les API REST', 'Docker', 'les tests unitaires']),
                    $student->getFullName()
                ))
                    ->setSentAt(new \DateTimeImmutable($faker->dateTimeBetween('-1 month')->format('Y-m-d H:i:s')))
                    ->setSender($student)
                    ->setReceiver($faker->randomElement($instructors))
                    ->setIsRead($faker->boolean(20)); // 20% de chances d'être lu

                $manager->persist($message);
            }
        }

        // Réponses des instructeurs
        $messages = $manager->getRepository(Message::class)->findAll();
        foreach ($messages as $originalMessage) {
            if ($faker->boolean(70)) { // 70% de chances d'avoir une réponse
                $response = new Message();
                
                $content = $faker->randomElement([
                    "Bonjour %s,\n\nMerci pour votre message. %s\n\nN'hésitez pas si vous avez d'autres questions.\n\nCordialement,\n%s",
                    "Bonjour %s,\n\n%s\n\nJ'espère que cela vous aide.\n\nBien à vous,\n%s",
                    "Bonjour %s,\n\n%s\n\nJe reste à votre disposition.\n\nCordialement,\n%s"
                ]);

                $response->setContent(sprintf(
                    $content,
                    $originalMessage->getSender()->getFirstName(),
                    $faker->paragraph(2),
                    $originalMessage->getReceiver()->getFullName()
                ))
                    ->setSentAt(new \DateTimeImmutable($faker->dateTimeBetween($originalMessage->getSentAt()->format('Y-m-d H:i:s'))->format('Y-m-d H:i:s')))
                    ->setSender($originalMessage->getReceiver())
                    ->setReceiver($originalMessage->getSender())
                    ->setIsRead($faker->boolean(50)); // 50% de chances d'être lu

                $manager->persist($response);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            StudentFixtures::class,
            InstructorFixtures::class,
        ];
    }
}
