<?php
namespace App\Validation;

use App\Exceptions\FormValidationException;

class ArticleFormValidation
{
    private array $data;
    private array $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @throws FormValidationException
     */
    public function passes():void
    {
        foreach ($this->data as $key => $value) {
            if (empty($value)) {
                $keyToUpper = ucfirst($key);
                $this->errors[$key][] = "{$keyToUpper} field is required";
            }
        }


        if (count($this->errors) > 0) {
            throw new FormValidationException();
        }
    }

    public function getErrors():array
    {
        return $this->errors;
    }
}