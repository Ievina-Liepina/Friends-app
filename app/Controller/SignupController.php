<?php
namespace App\Controller;

use App\View;
use App\Database;
use App\Redirect;
use App\Validation\Errors;
use App\Validation\SignupValidation;
use App\Exceptions\SignupValidationException;
use Doctrine\DBAL\Exception;

class SignupController
{
    public function signUp():View
    {
        return new View('/Users/signup', [
            'errors' => Errors::getAll(),
            'inputs' => $_SESSION['inputs'] ?? []
        ]);
    }

    /**
     * @throws Exception
     */
    public function signUpUser():Redirect
    {
        try {
            $validator = (new SignupValidation($_POST));
            $validator->passes();
        } catch (SignupValidationException $exception) {
            $_SESSION['errors'] = $validator->getErrors();
            $_SESSION['inputs'] = $_POST;
            return new Redirect("/signup");
        }


        $hashedPwd = password_hash($_POST["password"], PASSWORD_DEFAULT);


        Database::connection()
            ->insert('users', [
                'email' => $_POST['email'],
                'password' => $hashedPwd,
            ]);

        $createdUser = Database::connection()
            ->createQueryBuilder()
            ->select('id')
            ->from('users')
            ->where("email = ?")
            ->setParameter(0, $_POST['email'])
            ->fetchAllAssociative();

        Database::connection()
            ->insert('user_profiles', [
                'user_id' => $createdUser[0]['id'],
                'name' => $_POST['name'],
                'surname' => $_POST['surname'],
                'birthday' => $_POST['birthday']
            ]);

        return new Redirect("/articles");
    }
}