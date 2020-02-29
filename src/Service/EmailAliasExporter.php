<?php


namespace App\Service;

use App\Repository\EmailRepository;
use Symfony\Component\HttpKernel\Exception\HttpException;
use ZipArchive;

final class EmailAliasExporter
{

    private $repository;

    public function __construct(EmailRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param array $aliases
     * @param array $tags
     *
     * @return string
     */
    public function getZipArchve(array $aliases, array $tags): string
    {
        $data = $this->process($aliases, $tags);

        $zip = new ZipArchive();
        $filename = "/tmp/export.zip";
        if ($zip->open($filename, ZipArchive::CREATE) !== true) {
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

    /**
     * @param array $aliases
     * @param array $tags
     *
     * @return array
     */
    private function process(array $aliases, array $tags): array
    {
        $data = [];
        foreach ($aliases as $id) {
            $alias = $this->repository->find($id);

            if ($alias !== null) {
                $data['alias'][] = [
                    'email' => $alias->getTarget(),
                    'alias' => $alias->getAlias(),
                ];
            }

            if (in_array($id, $tags)) {
                foreach ($alias->getTags() as $tag) {
                    $data['tags'][] = [
                        'email' => $alias->getTarget(),
                        'alias' => $alias->getAlias(),
                        'tag' => $tag,
                    ];
                }
            }
        }

        return $data;
    }

    /**
     * @param array  $data
     * @param string $filename
     *
     * @return string
     */
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
