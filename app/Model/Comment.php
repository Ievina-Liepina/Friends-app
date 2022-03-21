<?php
namespace App\Model;

class Comment
{
    private string $name;
    private string $surname;
    private string $commentText;
    private int $authorId;
    private int $articleId;
    private string $createdAt;
    private int $id;

    public function __construct(string $name, string $surname, string $comment, int $authorId, int $articleId, string $createdAt, int $id)
    {
        $this->name = $name;
        $this->surname = $surname;
        $this->commentText = $comment;
        $this->authorId = $authorId;
        $this->articleId = $articleId;
        $this->createdAt = $createdAt;
        $this->id = $id;
    }
    public function getName():string
    {
        return $this->name;
    }
    public function getSurname():string
    {
        return $this->surname;
    }
    public function getComment():string
    {
        return $this->commentText;
    }
    public function getAuthorId():int
    {
        return $this->authorId;
    }
    public function getArticleId():int
    {
        return $this->articleId;
    }
    public function getCreatedAt():string
    {
        return $this->createdAt;
    }
    public function getId():int
    {
        return $this->id;
    }
}