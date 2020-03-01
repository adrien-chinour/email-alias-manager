<?php

namespace App\Controller;

use App\Service\AliasApiInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/target")
 */
final class TargetEmailApiController
{

    private $api;

    public function __construct(AliasApiInterface $api)
    {
        $this->api = $api;
    }

    /**
     * @Route("/", name="api_target_all")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function all()
    {
        return new JsonResponse($this->api->getEmails());
    }
}
