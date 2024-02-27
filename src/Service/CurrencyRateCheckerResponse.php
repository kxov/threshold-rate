<?php

declare(strict_types=1);

namespace App\Service;

class CurrencyRateCheckerResponse
{
    private ?array $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function getMessage(): ?array
    {
        return $this->message;
    }
}
