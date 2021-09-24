<?php

namespace App\Service;

use Symfony\Component\Validator\ConstraintViolation;

class ValidatorHelper {
    public static function violationsToArrayOfStrings($violations): array
    {
        $errors = [];

        /**@var ConstraintViolation $violation */
        foreach ($violations as $violation) {
            $name = self::getPropertyName($violation->getPropertyPath());
            $errors[$name] = $violation->getMessage();
        }

        return $errors;
    }

    private static function getPropertyName(string $path): string
    {
        preg_match('%\[(.*)\]%', $path, $m);
        return $m[1];
    }
}
