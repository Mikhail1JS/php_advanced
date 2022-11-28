<?php

namespace Project\Api\Blog;


class Post
{
    /**
     * @param UUID $uuid
     * @param User $authorUuid
     * @param string $title
     * @param string $text
     */
    public function __construct
    (
        private UUID $uuid ,
        private User $authorUuid,
        private string $title,
        private string $text
    ) {

    }

    public function __toString():string
    {
        return "User with id {$this->getAuthorUuid()} wrote post:\n id - {$this->uuid()};\n title-{$this->getTitle()};\n text-{$this->getText()} ";
    }



    public function uuid():string {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getAuthorUuid ():string {
        return $this->authorUuid->uuid();
    }

    /**
     * @param string $title
     * @return void
     */
    public function setTitle(string $title):void {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle():string {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getText():string {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }
}