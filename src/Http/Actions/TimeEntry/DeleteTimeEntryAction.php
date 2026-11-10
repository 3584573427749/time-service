<?php

declare(strict_types=1);

namespace App\Http\Actions\TimeEntry;

use App\Application\Handlers\TimeEntry\DeleteTimeEntryHandler;
use App\Domain\ValueObjects\TimeEntryId;
use App\Http\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class DeleteTimeEntryAction extends Action {
    public function __construct(LoggerInterface $logger, private DeleteTimeEntryHandler $handler) {
        parent::__construct($logger);
    }

    protected function action() : Response {
        $id = $this->request->getAttribute('id');

        $timeEntryId = new TimeEntryId($id);

        $this->handler->handle($timeEntryId);

        return $this->response->withStatus(204);
    }
}
