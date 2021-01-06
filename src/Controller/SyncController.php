<?php

namespace App\Controller;

use App\Entity\Alias;
use App\Repository\AliasRepository;
use App\Service\AliasApiInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sync")
 */
final class SyncController extends AbstractController
{

    private AliasApiInterface $api;

    private AliasRepository $repository;

    public function __construct(AliasApiInterface $api, AliasRepository $repository)
    {
        $this->api = $api;
        $this->repository = $repository;
    }

    /**
     * @Route("/", name="sync_index")
     */
    public function index()
    {
        return $this->redirectToRoute('sync_diff');
    }

    /**
     * @Route("/diff", name="sync_diff")
     */
    public function diff()
    {
        $diff = [];
        foreach ($this->api->getEmails() as $email) {
            $local = $this->repository->getAlias($email);
            $distant = $this->api->getAlias($email);

            $local = array_map(function (Alias $alias) {
                return $alias->getAliasEmail();
            }, $local);

            foreach (array_diff($distant, $local) as $alias) {
                $diff[] = [
                    'email' => $email,
                    'alias' => $alias,
                    'exist' => 'distant',
                ];
            }

            foreach (array_diff($local, $distant) as $alias) {
                $diff[] = [
                    'email' => $email,
                    'alias' => $alias,
                    'exist' => 'local',
                ];
            }
        }

        return $this->render('sync/diff.html.twig', ['diff' => $diff]);
    }

    /**
     * @Route("/add", name="sync_add", methods={"POST"})
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function add(Request $request)
    {
        $aliases = $request->request->get('alias');
        $emails = $request->request->get('email');
        $exists = $request->request->get('exist');

        if (empty($aliases)) {
            $this->addFlash('warning', 'Aucun alias à synchroniser');
            return $this->redirectToRoute('sync_diff');
        }

        foreach ($aliases as $key => $alias) {
            if ($exists[$key] === 'local') {
                $this->api->addAlias($emails[$key], $alias);
            } else {
                $email = (new Alias())
                    ->setRealEmail($emails[$key])
                    ->setAliasEmail($alias);
                $this->repository->save($email);
            }
        }

        $this->addFlash('success', 'Synchronisation terminée');
        return $this->redirectToRoute('sync_diff');
    }
}
