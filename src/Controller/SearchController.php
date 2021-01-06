<?php

namespace App\Controller;

use App\Repository\AliasRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/search")
 */
final class SearchController extends AbstractController
{

    /**
     * @Route("/alias", name="search_alias")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Repository\AliasRepository           $repository
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function alias(Request $request, AliasRepository $repository)
    {
        $search = $request->query->get('alias');

        return $this->render(
            'search/alias.html.twig',
            ['results' => $repository->search($search), 'search' => $search]
        );
    }
}
