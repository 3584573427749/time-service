<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use RuntimeException;

class UnauthorizedException extends RuntimeException {
    /**
     * @param array<string, string> $details
     */
    public function __construct(string $message, private array $details = []) {
        parent::__construct($message);
    }

    /**
     * @return string[]
     */
    public function getDetails() : array {
        return $this->details;
    }
}
