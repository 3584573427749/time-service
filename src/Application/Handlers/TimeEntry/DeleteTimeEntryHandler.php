<?php

declare(strict_types=1);

namespace App\Application\Handlers\TimeEntry;

use App\Domain\ValueObjects\TimeEntryId;

class DeleteTimeEntryHandler extends TimeEntryHandler {
    public function handle(TimeEntryId $id) : void {
        $this->repository->delete($id);
    }
}
