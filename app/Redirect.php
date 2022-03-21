<?php
namespace App;

class Redirect
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }
    public function getPath():string
    {
        return $this->path;
    }
}