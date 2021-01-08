<?php

namespace App\Controller;

use App\Repository\AliasRepository;
use App\Service\EmailAliasExporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/export")
 */
final class ExportController extends AbstractController
{
    /**
     * @Route("/", name="export_select")
     */
    public function selection(AliasRepository $repository): Response
    {
        return $this->render('export/select.html.twig', [
            'aliases' => $repository->findAll()
        ]);
    }

    /**
     * @Route("/csv", name="export_csv")
     *
     * @return BinaryFileResponse|RedirectResponse
     */
    public function exportCSV(Request $request, EmailAliasExporter $exporter)
    {
        $checked = function ($checkbox) {
            return 'on' === $checkbox;
        };

        $filename = $exporter->getZipArchve(
            array_keys(array_filter($request->request->get('alias') ?? [], $checked)),
        );

        if (!file_exists($filename)) {
            $this->addFlash('warning', 'Aucun alias ou tag à exporter');

            return $this->redirectToRoute('export_select');
        }

        $response = new BinaryFileResponse($filename);
        $response->headers->set('Content-Type', 'application/zip');

        $response
            ->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'export-alias.zip')
            ->deleteFileAfterSend();

        return $response;
    }
}
