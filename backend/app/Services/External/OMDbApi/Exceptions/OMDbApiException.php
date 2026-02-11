<?php

namespace App\Services\External\OMDbApi\Exceptions;

use RuntimeException;

class OMDbApiException extends RuntimeException
{
    public function __construct(
        string                  $message,
        private readonly int    $status = 502,
        private readonly ?array $payload = null,
    ) {
        parent::__construct($message);
    }

    public function status(): int { return $this->status; }

    public function payload(): ?array { return $this->payload; }

}
