<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class DeleteUserCommand extends Command
{
    private UserRepository $repository;

    public function __construct(UserRepository $repository, string $name = null)
    {
        parent::__construct($name);
        $this->repository = $repository;
    }

    protected function configure()
    {
        $this
            ->setName('app:user:delete')
            ->setAliases(['a:u:d'])
            ->setDescription('Delete an existing admin user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $users = array_map(function (User $user) {
            return $user->getUsername();
        }, $this->repository->findAll());

        $username = $io->choice('User to delete', $users);

        $user = $this->repository->findOneBy(['uuid' => $username]);
        if (null === $user) {
            $io->warning('User not found.');

            return 1;
        }

        $this->repository->delete($user);
        $io->success("User '$username' removed.");

        return 0;
    }
}
