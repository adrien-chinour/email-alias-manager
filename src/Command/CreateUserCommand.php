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

    private $encoder;

    private $repository;

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
            ->setDescription('Permet de créer un utilisateur');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $username = $io->ask('Username');
        $password = $io->askHidden('Password');

        $user = (new User())
            ->setUuid($username)
            ->setRoles(['ROLE_ADMIN', 'ROLE_USER']);

        $user->setPassword($this->encoder->encodePassword($user, $password));

        $this->repository->save($user);

        return 0;
    }

}
