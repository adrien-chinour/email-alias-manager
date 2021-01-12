<?php

namespace App\Controller;

use App\Entity\Alias;
use App\Form\AliasType;
use App\Repository\AliasRepository;
use App\Service\AliasService;
use App\Service\EmailAliasExporter;
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
    private TranslatorInterface $translator;

    private AliasService $aliasService;

    public function __construct(TranslatorInterface $translator, AliasService $aliasService)
    {
        $this->translator = $translator;
        $this->aliasService = $aliasService;
    }

    /**
     * @Route("/", name="alias_index", methods={"GET"})
     */
    public function index(Request $request, AliasRepository $repository): Response
    {
        $search = $request->query->get('search');

        $pagination = $repository->paginate($request->query->getInt('page', 1), $search);
        $pagination->setCustomParameters(['align' => 'center']);

        return $this->render('alias/index.html.twig', [
            'aliases' => $pagination,
            'search' => $search,
            'exportFormat' => EmailAliasExporter::SUPPORTED_FORMAT,
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
            $alias = $form->getData();
            $this->aliasService->add($alias);
            $this->addFlash('success', $this->translator->trans('Alias {alias} added', ['{alias}' => $alias->getAliasEmail()]));

            return $this->redirectToRoute('alias_index');
        }

        return $this->render('alias/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/{id}", name="alias_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Alias $alias): RedirectResponse
    {
        $email = $alias->getAliasEmail();
        if ($this->isCsrfTokenValid('delete' . $alias->getId(), $request->request->get('_token'))) {
            $this->aliasService->delete($alias);
            $this->addFlash('danger', $this->translator->trans('Alias {alias} has been deleted', ['alias' => $email]));
        }

        return $this->redirectToRoute('alias_index');
    }
}
