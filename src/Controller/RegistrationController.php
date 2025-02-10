<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\Instructor;
use App\Form\StudentRegistrationType;
use App\Form\InstructorRegistrationType;
use App\Security\LoginFormAuthenticator;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/choose_type.html.twig');
    }

    #[Route('/register/student', name: 'app_student_register')]
    public function registerStudent(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        UserAuthenticatorInterface $userAuthenticator,
        LoginFormAuthenticator $authenticator
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $student = new Student();
        $form = $this->createForm(StudentRegistrationType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $student->setPassword(
                $userPasswordHasher->hashPassword(
                    $student,
                    $form->get('plainPassword')->getData()
                )
            );
            
            $student->setRoles(['ROLE_STUDENT']);
            $student->setUpdatedAt(new DateTimeImmutable());
            $student->setProgress([]);

            $entityManager->persist($student);
            $entityManager->flush();

            return $userAuthenticator->authenticateUser(
                $student,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/student_register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/register/instructor', name: 'app_instructor_register')]
    public function registerInstructor(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        UserAuthenticatorInterface $userAuthenticator,
        LoginFormAuthenticator $authenticator
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $user = new Instructor();
        $form = $this->createForm(InstructorRegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            
            $user->setRoles(['ROLE_INSTRUCTOR']);

            $entityManager->persist($user);
            $entityManager->flush();

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/instructor_register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
} 