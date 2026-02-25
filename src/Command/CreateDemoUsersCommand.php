<?php

namespace App\Command;

use App\Repository\UserRepository;
use App\Security\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-demo-users',
    description: 'Creates test demo users with different roles for JWT testing',
)]
class CreateDemoUsersCommand extends Command
{
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // 1. Crear simple user@test.com
        $userObj1 = new User('', 'user@test.com', 'password', ['ROLE_USER']);
        $hashedPassword1 = $this->passwordHasher->hashPassword($userObj1, '123456');
        $this->userRepository->insertUser('user@test.com', $hashedPassword1, ['ROLE_USER']);
        
        $io->success('User "user@test.com" con pwd "123456" y rol [ROLE_USER] creado.');

        // 2. Crear administrador admin@test.com
        $userObj2 = new User('', 'admin@test.com', 'adminpass', ['ROLE_ADMIN']);
        $hashedPassword2 = $this->passwordHasher->hashPassword($userObj2, '123456');
        $this->userRepository->insertUser('admin@test.com', $hashedPassword2, ['ROLE_ADMIN', 'ROLE_USER']);

        $io->success('Admin "admin@test.com" con pwd "123456" y rol [ROLE_ADMIN] creado.');

        return Command::SUCCESS;
    }
}
