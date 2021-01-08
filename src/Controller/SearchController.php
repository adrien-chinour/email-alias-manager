<?php

namespace App\Controller;

use App\Repository\AliasRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/search")
 */
final class SearchController extends AbstractController
{
    /**
     * @Route("/alias", name="search_alias")
     */
    public function alias(Request $request, AliasRepository $repository): Response
    {
        $search = $request->query->get('alias');

        return $this->render('search/alias.html.twig', [
            'results' => $search ? $repository->search($search) : [],
            'search' => $search
        ]);
    }
}
