<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    #[Route('/cart', name: 'app_cart')]
    public function index(): Response
    {
        // Vérifier si l'utilisateur est connecté
        $this->denyAccessUnlessGranted('PUBLIC_ACCESS');

        return $this->render('cart/index.html.twig', [
            'cart' => $this->getCart(),
        ]);
    }

    private function getCart(): array
    {
        $session = $this->requestStack->getSession();
        return $session->get('cart', []);
    }
} 