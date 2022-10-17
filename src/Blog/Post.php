<?php

namespace Project\Api\Blog;


class Post
{
    /**
     * @param int $id
     * @param User $authorId
     * @param string $title
     * @param string $text
     */
    public function __construct
    (
        private int $id ,
        private User $authorId,
        private string $title,
        private string $text
    ) {

    }

    public function __toString():string
    {
        return "User with id {$this->getAuthorId()} wrote post:\n id - {$this->getId()};\n title-{$this->getTitle()};\n text-{$this->getText()} ";
    }

    /**
     * @param int $id
     * @return void
     */
    public function setId (int $id):void {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId():int {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getAuthorId ():int {
        return $this->authorId->getId();
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