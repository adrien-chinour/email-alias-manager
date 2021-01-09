<?php

namespace App\Service;

use App\Repository\AliasRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

final class EmailAliasExporter
{
    private AliasRepository $repository;

    public function __construct(AliasRepository $repository)
    {
        $this->repository = $repository;
    }

    public function get(string $format): string
    {
        $filesystem = new Filesystem();
        $filename = $filesystem->tempnam(sys_get_temp_dir(), 'alias_export_', ".$format");
        $filesystem->dumpFile($filename, $this->serialize($format));

        return $filename;
    }

    private function serialize(string $format): string
    {
        if (!in_array($format, ['csv', 'json', 'xml'])) {
            throw new \InvalidArgumentException(sprintf("'%s' is not a valid format. Authorized format are : %s", $format, implode(', ', ['csv', 'json', 'xml'])));
        }
        $serializer = new Serializer([new ObjectNormalizer()], [new XmlEncoder(), new JsonEncoder(), new CsvEncoder()]);

        return $serializer->serialize($this->repository->findAll(), $format);
    }
}
