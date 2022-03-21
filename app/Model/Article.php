<?php
namespace App\Model;

class Article
{
    private string $author;
    private int $authorId;
    private string $title;
    private string $description;
    private string $createdAt;
    private ?int $id;

    public function __construct(string $author, int $authorId, string $title, string $description, string $createdAt, ?int $id = null)
    {
        $this->author = $author;
        $this->authorId = $authorId;
        $this->title = $title;
        $this->description = $description;
        $this->createdAt = $createdAt;
        $this->id = $id;
    }

    public function getAuthor():string
    {
        return $this->author;
    }
    public function getAuthorId():int
    {
        return $this->authorId;
    }
    public function getTitle():string
    {
        return $this->title;
    }
    public function getDescription():string
    {
        return $this->description;
    }
    public function getCreatedAt():string
    {
        return $this->createdAt;
    }
    public function getId():string
    {
        return $this->id;
    }
}