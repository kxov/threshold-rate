<?php

declare(strict_types=1);

namespace App\Service;

class CurrencyRateCheckerResponse
{
    private ?string $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}
