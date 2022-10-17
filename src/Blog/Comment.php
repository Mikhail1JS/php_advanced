<?php

namespace Project\Api\Blog;

class Comment
{

    /**
     * @param int $id
     * @param User $authorId
     * @param Post $postId
     * @param string $text
     */
    public function __construct
    (
        private int $id,
        private User $authorId,
        private Post $postId,
        private string $text
    )
    {



    }

    public function __toString():string{
        return "Comment with id {$this->getId()} :\n Author id : {$this->getAuthorId()};\n To post : {$this->getPostId()};\n Text : {$this->getText()}";

    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }


    /**
     * @return int
     */
    public function getAuthorId(): int
    {
        return $this->authorId->getId();
    }


    public function getPostId(): int
    {
        return $this->postId->getId();
    }
}