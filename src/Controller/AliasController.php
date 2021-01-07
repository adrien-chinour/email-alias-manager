<?php

namespace App\Controller;

use App\Entity\Alias;
use App\Form\AliasType;
use App\Repository\AliasRepository;
use App\Service\AliasApiInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/alias")
 */
final class AliasController extends AbstractController
{
    private AliasApiInterface $api;

    private AliasRepository $repository;

    private TranslatorInterface $translator;

    public function __construct(AliasApiInterface $api, AliasRepository $repository, TranslatorInterface $translator)
    {
        $this->api = $api;
        $this->repository = $repository;
        $this->translator = $translator;
    }

    /**
     * @Route("/", name="alias_index", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        return $this->render(
            'alias/index.html.twig',
            ['aliases' => $this->repository->paginate($request->query->getInt('page', 1))]
        );
    }

    /**
     * @Route("/new", name="alias_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $form = $this->createForm(AliasType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Alias $alias */
            $alias = $form->getData();

            $this->repository->save($alias);
            $this->api->addAlias($alias->getRealEmail(), $alias->getAliasEmail());
            $this->addFlash('success', 'Alias ajouté !');

            return $this->redirectToRoute('alias_index');
        }

        return $this->render('alias/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/{id}/edit", name="alias_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Alias $alias): Response
    {
        $form = $this->createForm(AliasType::class, $alias, ['edition' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->save($alias);

            return $this->redirectToRoute('alias_index');
        }

        return $this->render('alias/edit.html.twig', ['alias' => $alias, 'form' => $form->createView()]);
    }

    /**
     * @Route("/{id}", name="alias_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Alias $alias): RedirectResponse
    {
        if ($this->isCsrfTokenValid(
            'delete'.$alias->getId(),
            $request->request->get('_token')
        )) {
            $this->api->deleteAlias($alias->getRealEmail(), $alias->getAliasEmail());
            $this->repository->delete($alias);
        }

        return $this->redirectToRoute('alias_index');
    }
}
