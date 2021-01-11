<?php

namespace App\Controller;

use App\Service\EmailAliasExporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/export")
 */
final class ExportController extends AbstractController
{
    /**
     * @Route("/", name="alias_export")
     */
    public function export(Request $request, EmailAliasExporter $exporter): BinaryFileResponse
    {
        $format = $request->query->get('format', 'csv');

        return $this->file($exporter($format), "alias-export.$format");
    }
}
