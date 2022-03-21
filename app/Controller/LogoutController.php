<?php
namespace App\Controller;

use App\Redirect;

session_start();
session_unset();
session_destroy();

class LogoutController
{
    public static function logout():Redirect
    {
        return new Redirect("/login");
    }
}