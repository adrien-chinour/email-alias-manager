<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

final class IndexController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/health-check", name="health_check")
     */
    public function health(): JsonResponse
    {
        // return ellias as app for application mobile sync check
        return new JsonResponse(['app' => 'ellias'], 200);
    }
}
