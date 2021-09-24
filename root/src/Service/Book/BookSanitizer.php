<?php

namespace App\Service\Book;

use App\Entity\Book;
use App\Service\Sanitizer\SanitizerService;

class BookSanitizer {

    public static function sanitizeDeleteData(array $inputData): array
    {
        $outputData = [];

        if (isset($inputData[Book::ID])) {
            $outputData[Book::ID] = SanitizerService::sanitizeIntNumber($inputData[Book::ID]);
        }

        return $outputData;
    }

    public static function sanitizeData(array $inputData): array
    {
        $outputData = [];

        if (isset($inputData[Book::ID])) {
            $outputData[Book::ID] = SanitizerService::sanitizeIntNumber($inputData[Book::ID]);
        }

        if (isset($inputData[Book::NAME])) {
            $outputData[Book::NAME] = SanitizerService::sanitizeString($inputData[Book::NAME]);
        }

        if (isset($inputData[Book::ISBN])) {
            $outputData[Book::ISBN] = SanitizerService::sanitizeIntNumber($inputData[Book::ISBN]);
        }

        if (isset($inputData[Book::NUMBER_PAGES])) {
            $outputData[Book::NUMBER_PAGES] = SanitizerService::sanitizeIntNumber($inputData[Book::NUMBER_PAGES]);
        }

        if (isset($inputData[Book::PUBLISH_YEAR])) {
            $outputData[Book::PUBLISH_YEAR] = SanitizerService::sanitizeIntNumber($inputData[Book::PUBLISH_YEAR]);
        }

        if (isset($inputData[Book::AUTHORS])) {
            $outputData[Book::AUTHORS] = $inputData[Book::AUTHORS];
        }

        return $outputData;
    }
}
