<?php
namespace App\Controller;

use App\View;
use App\Redirect;
use App\Database;
use Doctrine\DBAL\Exception;
session_start();
class LoginController
{
    public function login():View
    {
        return new View('/Users/login');
    }

    /**
     * @throws Exception
     */
    public function loginUser():Redirect
    {
        if (isset($_POST['submit'])) {
            $databaseInfo = Database::connection()
                ->createQueryBuilder()
                ->select('*')
                ->from('users')
                ->where("email = ?")
                ->setParameter(0, $_POST["email"])
                ->fetchAllAssociative();
            if ($databaseInfo != null) {
                $user = $databaseInfo;


                $pwdHashed = $user[0]["password"];
                $checkPwd = password_verify($_POST['password'], $pwdHashed);

                if (!$checkPwd) {
                    return new Redirect('/login');
                }

                $userProfile = Database::connection()
                    ->createQueryBuilder()
                    ->select('*')
                    ->from('user_profiles')
                    ->where("user_id = ?")
                    ->setParameter(0, $user[0]['id'])
                    ->fetchAllAssociative();


                $_SESSION["userid"] = htmlentities($user[0]["id"]);
                $_SESSION["name"] = htmlentities($userProfile[0]["name"]);
            } else {
                return new Redirect("/login");
            }
        }


        return new Redirect("/articles");
    }
}