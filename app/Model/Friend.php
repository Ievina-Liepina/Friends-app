<?php
namespace App\Model;

class Friend
{
    private string $name;
    private string $surname;
    private int $friendId;

    public function __construct(string $name, string $surname, int $friendId)
    {
        $this->name = $name;
        $this->surname = $surname;
        $this->friendId = $friendId;
    }

    public function getName():string
    {
        return $this->name;
    }
    public function getSurname():string
    {
        return $this->surname;
    }
    public function getFriendId():int
    {
        return $this->friendId;
    }
}