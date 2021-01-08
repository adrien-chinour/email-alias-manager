<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/health-check")
 */
class HealthCheckController
{
    /**
     * @Route("/", name="health_check")
     */
    public function health(): JsonResponse
    {
        // return ellias as app for application mobile sync check
        return new JsonResponse(['app' => 'ellias'], 200);
    }
}
