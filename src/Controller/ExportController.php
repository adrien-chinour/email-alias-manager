<?php

namespace App\Controller;

use App\Repository\EmailRepository;
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
     * @param EmailRepository $repository
     *
     * @return Response
     */
    public function selection(EmailRepository $repository)
    {
        return $this->render(
            'export/select.html.twig',
            ['aliases' => $repository->findAll()]
        );
    }

    /**
     * @Route("/csv", name="export_csv")
     * @param Request $request
     * @param EmailAliasExporter $exporter
     *
     * @return BinaryFileResponse|RedirectResponse
     */
    public function exportCSV(Request $request, EmailAliasExporter $exporter)
    {
        $checked = function ($checkbox) {
            return $checkbox === "on";
        };

        $filename = $exporter->getZipArchve(
            array_keys(array_filter($request->request->get('alias') ?? [], $checked)),
            array_keys(array_filter($request->request->get('tags') ?? [], $checked))
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
