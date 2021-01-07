<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class IndexController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     *
     * @return RedirectResponse
     */
    public function index()
    {
        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/health-check", name="health_check")
     *
     * @return Response
     */
    public function health()
    {
        // return ellias as app for application mobile sync check
        return new JsonResponse(['app' => 'ellias'], 200);
    }
}
