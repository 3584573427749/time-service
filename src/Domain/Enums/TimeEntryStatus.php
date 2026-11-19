<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum TimeEntryStatus : string {
    case OPEN = 'open';
    case APPROVED = 'approved';
    case EXPORTED = 'exported';
}
