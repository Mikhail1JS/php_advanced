<?php

namespace Project\Api\Blog\Commands\FakeData;

use Project\Api\Blog\Comment;
use Project\Api\Blog\Post;
use Project\Api\Blog\Repositories\CommentsRepositories\CommentsRepositoriesInterface;
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
        private \Faker\Generator         $faker,
        private UsersRepositoryInterface $usersRepository,
        private PostsRepositoryInterface $postsRepository,
        private CommentsRepositoriesInterface $commentsRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName("fake-data:populate-db")
            ->setDescription("Populates DB with fake data")
            ->addOption(
                "users-number",
                "u",
                InputOption::VALUE_OPTIONAL,
                "Users number"
            )
            ->addOption(
                "posts-number",
                "p",
                InputOption::VALUE_OPTIONAL,
                "Posts number"
            );
    }

    protected function execute(
        InputInterface  $input,
        OutputInterface $output): int
    {

        $users = [];

        $usersNumber = empty($input->getOption("users-number")) ? 10 : $input->getOption("users-number");

        $postsNumber = empty($input->getOption("posts-number")) ? 20 : $input->getOption("users-number");

        for ($i = 0; $i < (int)$usersNumber; $i++) {
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln("User created: " . $user->username());
        }

        $posts = [];
        foreach ($users as $user) {
            for ($i = 0; $i < (int)$postsNumber; $i++) {
                $post = $this->createFakePost($user);
                $posts[] = $post;
                $output->writeln("Post created: " . $post->title());
            }
        }

        foreach ($posts as $post) {
            $user = $users[array_rand($users)];
            $comment = $this->createFakeComment($post, $user);

            $output->writeln("Comment created: " . $comment->uuid() );
        }

        return Command::SUCCESS;

    }

    private function createFakeUser(): User
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

    private function createFakePost(User $author): Post
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

    private function createFakeComment(Post $post , User $user): Comment {

        $comment = new Comment(
            UUID::random(),
            $user,
            $post,
            $this->faker->realText(50)
        );

        $this->commentsRepository->save($comment);

        return $comment;
    }

}