<?php

namespace App\Controller;

use App\Entity\Email;
use App\Repository\EmailRepository;
use App\Service\AliasApiInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sync")
 */
final class SyncController extends AbstractController
{

    private $api;

    private $repository;

    public function __construct(AliasApiInterface $api, EmailRepository $repository)
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

            foreach (array_diff($distant, $local) as $alias) {
                $diff[] = [
                    'email' => $email,
                    'alias' => $alias,
                    'is' => in_array($alias, $local) ? 'local' : 'distant',
                ];
            }
        }

        return $this->render('sync/diff.html.twig', ['diff' => $diff]);
    }

    /**
     * @Route("/add", name="sync_add", methods={"POST"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function add(Request $request)
    {
        $aliases = $request->request->get('alias');
        $emails = $request->request->get('email');

        if (empty($aliases)) {
            $this->addFlash('warning', 'Aucun alias à synchroniser');
            return $this->redirectToRoute('sync_diff');
        }

        foreach ($aliases as $key => $alias) {
            $email = (new Email())
                ->setTarget($emails[$key])
                ->setAlias($alias);
            $this->repository->save($email);
        }

        $this->addFlash('success', 'Synchronisation terminée');
        return $this->redirectToRoute('sync_diff');
    }
}
