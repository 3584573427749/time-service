<?php

declare(strict_types=1);

namespace App\Http\Actions\TimeEntry;

use App\Application\Handlers\TimeEntry\GetTimeEntryHandler;
use App\Domain\ValueObjects\TimeEntryId;
use App\Http\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class GetTimeEntryAction extends Action {
    public function __construct(LoggerInterface $logger, private GetTimeEntryHandler $handler) {
        parent::__construct($logger);
    }

    protected function action() : Response {
        $id = new TimeEntryId(
            $this->resolveArg('id'),
        );

        $dto = $this->handler->handle($id);

        return $this->respondWithData($dto);
    }
}
