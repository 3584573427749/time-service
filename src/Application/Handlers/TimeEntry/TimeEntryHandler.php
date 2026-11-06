<?php

declare(strict_types=1);

namespace App\Application\Handlers\TimeEntry;

use App\Domain\Repositories\TimeEntryRepository;

abstract class TimeEntryHandler {
    public function __construct(protected TimeEntryRepository $repository) {
    }

}
