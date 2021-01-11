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
    const SUPPORTED_FORMAT = ['csv', 'json', 'xml'];

    private AliasRepository $repository;

    private Serializer $serializer;

    private Filesystem $filesystem;

    public function __construct(AliasRepository $repository)
    {
        $this->repository = $repository;
        $this->serializer = new Serializer([new ObjectNormalizer()], [new XmlEncoder(), new JsonEncoder(), new CsvEncoder()]);
        $this->filesystem = new Filesystem();
    }

    public function __invoke(string $format): string
    {
        $filename = $this->filesystem->tempnam(sys_get_temp_dir(), 'alias_export_', ".$format");
        $this->filesystem->dumpFile($filename, $this->serialize($format));

        return $filename;
    }

    private function serialize(string $format): string
    {
        if (!in_array($format, self::SUPPORTED_FORMAT)) {
            throw new \InvalidArgumentException(sprintf("'%s' is not a valid format. Authorized formats are : [%s]", $format, implode(', ', self::SUPPORTED_FORMAT)));
        }

        return $this->serializer->serialize($this->repository->findAll(), $format);
    }
}
