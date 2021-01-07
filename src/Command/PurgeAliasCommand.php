<?php

namespace App\Command;

use App\Entity\Alias;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PurgeAliasCommand extends Command
{
    private SymfonyStyle $io;

    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager, string $name = null)
    {
        parent::__construct($name);
        $this->manager = $manager;
    }

    protected function configure()
    {
        $this
            ->setName('app:alias:purge')
            ->setAliases(['a:a:p'])
            ->addOption('force', 'f', InputOption::VALUE_NONE)
            ->setDescription('Purge alias database');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('force')) {
            $validation = $this->io->confirm('This command will removed all aliases on current database. Sure ?');

            if (!$validation) {
                $this->io->warning('Command aborded');

                return 0;
            }
        }

        $c = $this->manager->getConnection();
        $c->beginTransaction();
        try {
            $c->query(sprintf('DELETE FROM %s', $this->manager->getClassMetadata(Alias::class)->getTableName()));
            $c->commit();
        } catch (Exception $e) {
            $this->io->error($e->getMessage());

            return 1;
        }
        $this->io->success('Aliases purged.');

        return 0;
    }
}
