<?php

namespace App\Exceptions;

use Exception;

class ReceiptAnalysisException extends Exception
{
    public static function missingApiKey(): self
    {
        return new self('No Anthropic API key configured.');
    }

    public static function requestFailed(int $status, string $body): self
    {
        return new self("The receipt could not be analysed (Anthropic API responded with status {$status}: {$body}).");
    }

    public static function noResult(): self
    {
        return new self('The AI did not return any prices for this receipt.');
    }
}