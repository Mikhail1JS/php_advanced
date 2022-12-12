<?php

namespace Project\Api\Blog\Commands\Users;

use Project\Api\Blog\Exceptions\InvalidArgumentException;
use Project\Api\Blog\Exceptions\UserNotFoundException;
use Project\Api\Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Project\Api\Blog\User;
use Project\Api\Blog\UUID;
use Project\Api\Person\Name;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateUser extends Command
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName("users:update")
            ->setDescription("Update a user")
            ->addArgument(
                "uuid",
                InputArgument::REQUIRED,
                "UUID of a user to update"
            )
            ->addOption(
                "first-name",
                "f",
                InputOption::VALUE_OPTIONAL,
                "First name")
            ->addOption(
                "last-name",
                "l",
                InputOption::VALUE_OPTIONAL,
                "Last name"
            );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int
    {
       $firstName = $input->getOption("first-name");
       $lastName = $input->getOption("last-name");

       if(empty($firstName) && empty($lastName)){
           $output->writeln("Nothing to update");
           return Command::SUCCESS;
       }

       try {
           $uuid = new UUID($input->getArgument("uuid"));
           $user = $this->usersRepository->get($uuid);
       }catch(InvalidArgumentException | UserNotFoundException $e){
           $output->writeln($e->getMessage());
           return Command::SUCCESS;
       }

       $updatedName = new Name(
           first: empty($firstName) ? $user->name()->first() : $firstName,
           last: empty($lastName) ? $user->name()->last() : $lastName
       );

       $updatedUser = new User (
           uuid: $uuid,
           username: $user->username(),
           hashedPassword: $user->hashedPassword(),
           name: $updatedName
       );

       $this->usersRepository->save($updatedUser);

       $output->writeln("User updated: $uuid");

       return Command::SUCCESS;

    }

}