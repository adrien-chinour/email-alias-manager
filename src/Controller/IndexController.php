<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class IndexController extends AbstractController
{

    /**
     * @Route("/", name="app_index")
     * @return RedirectResponse
     */
    public function index()
    {
        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/health-check", name="health_check")
     * @return Response
     */
    public function health()
    {
        return new Response(null, 200);
    }
}
