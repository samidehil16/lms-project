<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentRegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Security\LoginFormAuthenticator;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        EntityManagerInterface $entityManager,
        UserAuthenticatorInterface $userAuthenticator,
        LoginFormAuthenticator $authenticator
    ): Response {
        $user = new Student();
        $form = $this->createForm(StudentRegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // Authentifier l'utilisateur automatiquement après l'inscription
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/student_register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/student/register', name: 'app_student_register')]
    public function registerStudent(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $student = new Student();
        $form = $this->createForm(StudentRegistrationType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode le mot de passe
            $student->setPassword(
                $userPasswordHasher->hashPassword(
                    $student,
                    $form->get('plainPassword')->getData()
                )
            );
            
            // Définir le rôle
            $student->setRoles(['ROLE_STUDENT']);
            $student->setUpdatedAt(new \DateTimeImmutable());
            $student->setProgress([]);

            $entityManager->persist($student);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte étudiant a été créé avec succès !');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/student_register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/instructor/register', name: 'app_instructor_register')]
    public function registerInstructor(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/instructor_register.html.twig');
    }
} 