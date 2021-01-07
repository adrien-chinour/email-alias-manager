<?php

namespace App\Provider\Fake;

use App\Provider\Fake\Models\Alias;
use App\Provider\Fake\Models\Email;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class FilesystemStorage
{
    private Serializer $serializer;

    private Filesystem $filesystem;

    private string $aliasFile;

    private string $emailFile;

    public function __construct()
    {
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $this->filesystem = new Filesystem();
        $this->aliasFile = $this->getDirectory().DIRECTORY_SEPARATOR.'aliases';
        $this->emailFile = $this->getDirectory().DIRECTORY_SEPARATOR.'emails';

        $this->initilalize();
    }

    private function initilalize()
    {
        if ($this->filesystem->exists($this->aliasFile) && $this->filesystem->exists($this->emailFile)) {
            return;
        }

        $emails = [
            new Email('email1@exemple.com'),
            new Email('email2@exemple.com'),
        ];
        $this->writeEmails($emails);

        $aliases = [
            new Alias('email1@exemple.com', 'alias-email1@exemple.com'),
            new Alias('email2@exemple.com', 'alias-email2@exemple.com'),
        ];
        $this->writeAliases($aliases);
    }

    public function readAliases(): array
    {
        return $this->read($this->aliasFile, Alias::class);
    }

    public function writeAliases(array $aliases): void
    {
        $this->write($this->aliasFile, $aliases);
    }

    public function readEmails(): array
    {
        return $this->read($this->emailFile, Email::class);
    }

    public function writeEmails(array $emails): void
    {
        $this->write($this->emailFile, $emails);
    }

    private function read(string $filename, string $class)
    {
        $data = [];
        foreach (json_decode(file_get_contents($filename), true) as $item) {
            $data[] = $this->serializer->deserialize(json_encode($item), $class, 'json');
        }

        return $data;
    }

    private function write(string $filename, $data)
    {
        file_put_contents($filename, $this->serializer->serialize($data, 'json'));
    }

    private function getDirectory()
    {
        $path = sys_get_temp_dir().DIRECTORY_SEPARATOR.'fake-alias-api';
        if ($this->filesystem->exists($path)) {
            return $path;
        } else {
            $this->filesystem->mkdir($path);
        }

        return $path;
    }
}
