<?php

namespace App\Service\Author;

use App\Entity\Author;
use App\Service\Sanitizer\SanitizerService;

class AuthorSanitizer {

    public static function sanitizeDeleteData(array $inputData): array
    {
        $outputData = [];

        if (isset($inputData[Author::ID])) {
            $outputData[Author::ID] = SanitizerService::sanitizeIntNumber($inputData[Author::ID]);
        }

        return $outputData;
    }

    public static function sanitizeData(array $inputData): array
    {
        $outputData = [];

        if (isset($inputData[Author::ID])) {
            $outputData[Author::ID] = SanitizerService::sanitizeIntNumber($inputData[Author::ID]);
        }

        if (isset($inputData[Author::NAME])) {
            $outputData[Author::NAME] = SanitizerService::sanitizeString($inputData[Author::NAME]);
        }

        if (isset($inputData[Author::SURNAME])) {
            $outputData[Author::SURNAME] = SanitizerService::sanitizeString($inputData[Author::SURNAME]);
        }

        if (isset($inputData[Author::PATRONYMIC])) {
            $outputData[Author::PATRONYMIC] = SanitizerService::sanitizeString($inputData[Author::PATRONYMIC]);
        }

        return $outputData;
    }
}
