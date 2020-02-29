<?php

namespace App\Controller;

use App\Repository\EmailRepository;
use App\Service\EmailAliasExporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/export")
 */
final class ExportController extends AbstractController
{

    /**
     * @Route("/", name="export_select")
     * @param \App\Repository\EmailRepository $repository
     *
     * @return \Symfony\Component\HttpFoundation\Response
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
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Service\EmailAliasExporter           $exporter
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
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
            throw new HttpException(404, "L'archive est vide");
        }
        $response = new BinaryFileResponse($filename);
        $response->headers->set('Content-Type', 'application/zip');
        $response->deleteFileAfterSend();

        return $response;
    }
}
