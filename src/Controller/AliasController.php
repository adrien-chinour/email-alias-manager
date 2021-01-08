<?php

namespace App\Controller;

use App\Entity\Alias;
use App\Form\AliasType;
use App\Provider\AliasApiInterface;
use App\Repository\AliasRepository;
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
        $pagination = $this->repository->paginate($request->query->getInt('page', 1));
        $pagination->setCustomParameters(['align' => 'center']);

        return $this->render('alias/index.html.twig', [
            'aliases' => $pagination
        ]);
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
     * @Route("/{id}", name="alias_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Alias $alias): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $alias->getId(), $request->request->get('_token'))) {
            $this->api->deleteAlias($alias->getRealEmail(), $alias->getAliasEmail());
            $this->repository->delete($alias);
        }

        return $this->redirectToRoute('alias_index');
    }
}
