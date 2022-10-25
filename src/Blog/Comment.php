<?php

namespace Project\Api\Blog;

class Comment
{

    /**
     * @param UUID $uuid
     * @param User $authorUuid
     * @param Post $postUuid
     * @param string $text
     */
    public function __construct
    (
        private UUID $uuid,
        private User $authorUuid,
        private Post $postUuid,
        private string $text
    )
    {



    }

    public function __toString():string{
        return "Comment with id {$this->uuid()} :\n Author id : {$this->getAuthorUuid()};\n To post : {$this->getPostUuid()};\n Text : {$this->getText()}";

    }

    /**
     * @return string
     */
    public function uuid(): string
    {
        return $this->uuid;
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
     * @return UUID
     */
    public function getAuthorUuid(): string
    {
        return $this->authorUuid->uuid();
    }


    public function getPostUuid(): string
    {
        return $this->postUuid->uuid();
    }
}