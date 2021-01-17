<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class CreateUserCommand extends Command
{
    private UserPasswordEncoderInterface $encoder;

    private UserRepository $repository;

    public function __construct(UserPasswordEncoderInterface $encoder, UserRepository $repository, string $name = null)
    {
        parent::__construct($name);
        $this->encoder = $encoder;
        $this->repository = $repository;
    }

    protected function configure()
    {
        $this
            ->setName('app:user:create')
            ->setAliases(['a:u:c'])
            ->setDescription('Create a new admin user');
    }

    /**
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $user = (new User())
            ->setUuid($io->ask('Username'))
            ->setRoles(['ROLE_ADMIN', 'ROLE_USER']);

        $user->setPassword($this->encoder->encodePassword($user, $io->askHidden('Password')));

        $this->repository->save($user);

        return 0;
    }
}
