<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Payment;
use App\Entity\Enrollment;
use App\Entity\Student;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends AbstractController
{
    public function __construct(
        private string $stripeSecretKey,
        private string $stripePublicKey,
        private EntityManagerInterface $entityManager
    ) {
        Stripe::setApiKey($this->stripeSecretKey);
    }

    #[Route('/payment/create-session/{id}', name: 'app_payment_create_session')]
    public function createSession(
        Course $course,
        UrlGeneratorInterface $urlGenerator
    ): Response {
        /** @var Student $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $course->getTitle(),
                        'description' => $course->getDescription(),
                    ],
                    'unit_amount' => (int)($course->getPrice() * 100), // Stripe utilise les centimes
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $urlGenerator->generate('app_payment_success', [
                'course_id' => $course->getId()
            ], UrlGeneratorInterface::ABSOLUTE_URL) . '&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $urlGenerator->generate('app_payment_cancel', [
                'course_id' => $course->getId()
            ], UrlGeneratorInterface::ABSOLUTE_URL),
            'customer_email' => $user->getEmail(),
            'metadata' => [
                'course_id' => $course->getId(),
                'user_id' => $user->getId()
            ],
        ]);

        return $this->redirect($checkoutSession->url);
    }

    #[Route('/payment/success', name: 'app_payment_success')]
    public function success(
        Request $request
    ): Response {
        $session_id = $request->query->get('session_id');
        $course_id = $request->query->get('course_id');
        
        $session = Session::retrieve($session_id);
        $course = $this->entityManager->getRepository(Course::class)->find($course_id);
        /** @var Student $user */
        $user = $this->getUser();

        if (!$course || !$user) {
            throw $this->createNotFoundException('Course or user not found');
        }

        // Créer le paiement
        $payment = new Payment();
        $payment->setStudent($user)
            ->setCourse($course)
            ->setPrice($course->getPrice())
            ->setPaymentMethod('stripe')
            ->setStatus('completed')
            ->setCreatedAt(new DateTimeImmutable())
            ->setPaidAt(new DateTimeImmutable())
            ->setTransactionId($session->payment_intent)
            ->setPaymentDetails(['stripe_session_id' => $session_id]);

        // Créer l'inscription
        $enrollment = new Enrollment();
        $enrollment->setStudent($user)
            ->setCourse($course)
            ->setEnrolledAt(new DateTimeImmutable());

        $this->entityManager->persist($payment);
        $this->entityManager->persist($enrollment);
        $this->entityManager->flush();

        $this->addFlash('success', 'Paiement réussi ! Vous êtes maintenant inscrit au cours.');
        return $this->redirectToRoute('app_course_show', ['id' => $course_id]);
    }

    #[Route('/payment/cancel/{course_id}', name: 'app_payment_cancel')]
    public function cancel(Course $course): Response
    {
        $this->addFlash('error', 'Le paiement a été annulé.');
        return $this->redirectToRoute('app_course_show', ['id' => $course->getId()]);
    }

    #[Route('/payment/checkout/{id}', name: 'app_payment_checkout')]
    public function checkout(Course $course): Response
    {
        /** @var Student $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('payment/checkout.html.twig', [
            'course' => $course,
            'stripe_public_key' => $this->stripePublicKey
        ]);
    }
} 