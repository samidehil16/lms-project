<?php

namespace App\DataFixtures;

use App\Entity\Payment;
use App\Entity\Student;
use App\Entity\Course;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class PaymentFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private string $projectDir)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $students = $manager->getRepository(Student::class)->findAll();
        $courses = $manager->getRepository(Course::class)->findAll();

        if (empty($students) || empty($courses)) {
            return;
        }

        $paymentMethods = ['card', 'paypal', 'stripe'];
        $statuses = [
            'completed' => 70,  // 70% de chances
            'pending' => 20,    // 20% de chances
            'failed' => 10      // 10% de chances
        ];

        foreach ($students as $student) {
            // Chaque étudiant achète entre 1 et 3 cours
            $numCourses = $faker->numberBetween(1, 3);
            $selectedCourses = $faker->randomElements($courses, $numCourses);
            
            foreach ($selectedCourses as $course) {
                $payment = new Payment();
                
                // Déterminer le statut selon les probabilités définies
                $status = $faker->randomElement(array_keys($statuses));
                
                $payment->setStudent($student)
                    ->setCourse($course)
                    ->setPrice($course->getPrice())
                    ->setPaymentMethod($faker->randomElement($paymentMethods))
                    ->setStatus($status)
                    ->setCreatedAt(new \DateTimeImmutable($faker->dateTimeBetween('-6 months')->format('Y-m-d H:i:s')));

                // Si le paiement est complété, ajouter une date de paiement
                if ($status === 'completed') {
                    $payment->setPaidAt(new \DateTimeImmutable($faker->dateTimeBetween(
                        $payment->getCreatedAt()->format('Y-m-d H:i:s'),
                        '+2 days'
                    )->format('Y-m-d H:i:s')));

                    // Générer un numéro de transaction pour les paiements complétés
                    $payment->setTransactionId(strtoupper($faker->bothify('PAY-####-####-????-????')));
                }

                // Ajouter des détails de paiement selon la méthode
                $paymentDetails = [];
                switch ($payment->getPaymentMethod()) {
                    case 'card':
                        $paymentDetails = [
                            'card_type' => $faker->creditCardType,
                            'last4' => $faker->numerify('####'),
                            'exp_month' => $faker->numberBetween(1, 12),
                            'exp_year' => $faker->numberBetween(date('Y'), date('Y') + 5)
                        ];
                        break;
                    case 'paypal':
                        $paymentDetails = [
                            'email' => $faker->email,
                            'payer_id' => $faker->bothify('PAY#####??????')
                        ];
                        break;
                    case 'stripe':
                        $paymentDetails = [
                            'stripe_id' => 'pi_' . $faker->bothify('????????????????????????????'),
                            'payment_intent' => 'pi_' . $faker->bothify('????????????????????????????')
                        ];
                        break;
                }
                
                $payment->setPaymentDetails($paymentDetails);
                $manager->persist($payment);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            StudentFixtures::class,
            CourseFixtures::class,
        ];
    }
}
