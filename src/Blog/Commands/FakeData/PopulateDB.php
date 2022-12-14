<?php

namespace Project\Api\Blog\Commands\FakeData;

use Project\Api\Blog\Post;
use Project\Api\Blog\Repositories\PostsRepositories\PostsRepositoryInterface;
use Project\Api\Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Project\Api\Blog\User;
use Project\Api\Blog\UUID;
use Project\Api\Person\Name;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateDB extends Command
{
    public function __construct(
        private \Faker\Generator $faker,
        private UsersRepositoryInterface $usersRepository,
        private PostsRepositoryInterface $postsRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
       $this
           ->setName("fake-data:populate-db")
           ->setDescription("Populates DB with fake data");
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output): int
{

    $users = [];

    for($i = 0; $i < 10; $i++){
        $user = $this->createFakeUser();
        $users[] = $user;
        $output->writeln("User created: " . $user->username());
    }

    foreach ($users as $user){
        for($i = 0; $i < 20 ; $i++) {
            $post = $this->createFakePost($user);
            $output->writeln("Post created: " . $post->title());
        }
    }

    return Command::SUCCESS;

}

    private function createFakeUser (): User
    {
        $user = User::createFrom(
            $this->faker->userName,
            $this->faker->password,
            new Name (
                $this->faker->firstName,
                $this->faker->lastName
            )
        );

        $this->usersRepository->save($user);

        return $user;
    }

    private function createFakePost (User $author): Post
    {
        $post = new Post(
            UUID::random(),
            $author,
            $this->faker->sentence(6, true),
            $this->faker->realText
        );

        $this->postsRepository->save($post);

        return $post;
    }

}