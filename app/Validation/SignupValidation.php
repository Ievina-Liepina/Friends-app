<?php
namespace App\Validation;

use App\Exceptions\SignupValidationException;

class SignupValidation
{
    private array $data;
    private array $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @throws SignupValidationException
     */
    public function passes():void
    {
        foreach ($this->data as $key => $value) {
            if (empty($value)) {
                $keyToUpper = ucfirst($key);
                $this->errors[$key][] = "{$keyToUpper} field is required";
            }
        }
        // Invalid Name and Surname
        if (!preg_match("/^[a-zA-Z0-9]*$/", $this->data['name'])) {
            $this->errors["invalidName"][] = "Invalid name";
        }
        if (!preg_match("/^[a-zA-Z0-9]*$/", $this->data['surname'])) {
            $this->errors["invalidSurname"][] = "Invalid surname";
        }

        if (count($this->errors) > 0) {
            throw new SignupValidationException();
        }
    }

    public function getErrors():array
    {
        return $this->errors;
    }
}