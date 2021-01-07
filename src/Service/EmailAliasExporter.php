<?php

namespace App\Service;

use App\Repository\AliasRepository;
use Symfony\Component\HttpKernel\Exception\HttpException;
use ZipArchive;

final class EmailAliasExporter
{
    private AliasRepository $repository;

    public function __construct(AliasRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getZipArchve(array $aliases, array $tags): string
    {
        $data = $this->process($aliases, $tags);

        $zip = new ZipArchive();
        $filename = '/tmp/export.zip';
        if (true !== $zip->open($filename, ZipArchive::CREATE)) {
            throw new HttpException(500, "Impossible d'ouvrir le fichier <$filename>\n");
        }

        if (isset($data['alias'])) {
            $alias = $this->toCsv($data['alias'], '/tmp/alias.csv');
            $zip->addFromString(basename($alias), file_get_contents($alias));
        }

        if (isset($data['tags'])) {
            $tags = $this->toCsv($data['tags'], '/tmp/tags.csv');
            $zip->addFromString(basename($tags), file_get_contents($tags));
        }
        $zip->close();

        return $filename;
    }

    private function process(array $aliases, array $tags): array
    {
        $data = [];
        foreach ($aliases as $id) {
            $alias = $this->repository->find($id);

            if (null !== $alias) {
                $data['alias'][] = [
                    'email' => $alias->getRealEmail(),
                    'alias' => $alias->getAliasEmail(),
                ];
            }

            if (in_array($id, $tags)) {
                foreach ($alias->getTags() as $tag) {
                    $data['tags'][] = [
                        'email' => $alias->getRealEmail(),
                        'alias' => $alias->getAliasEmail(),
                        'tag' => $tag,
                    ];
                }
            }
        }

        return $data;
    }

    private function toCsv(array $data, string $filename): string
    {
        $file = fopen($filename, 'w+');

        fputcsv($file, array_keys($data[0]));
        foreach ($data as $line) {
            fputcsv(
                $file,
                $line
            );
        }

        fclose($file);

        return $filename;
    }
}
