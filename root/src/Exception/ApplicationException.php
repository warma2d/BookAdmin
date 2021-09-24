<?php

namespace App\Exception;

class ApplicationException extends \Exception {

    private $errors;

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }
}
