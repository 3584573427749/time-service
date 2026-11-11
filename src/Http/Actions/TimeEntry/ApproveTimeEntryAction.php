<?php

declare(strict_types=1);

namespace App\Http\Actions\TimeEntry;

use App\Application\Handlers\TimeEntry\ApproveTimeEntryHandler;
use App\Domain\ValueObjects\TimeEntryId;
use App\Domain\ValueObjects\UserId;
use App\Http\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class ApproveTimeEntryAction extends Action {
    public function __construct(LoggerInterface $logger, private ApproveTimeEntryHandler $handler) {
        parent::__construct($logger);
    }

    protected function action() : Response {
        $id = $this->request->getAttribute('id');
        // TODO:
        // Hämta currentUser från AuthenticationMiddleware
        $currentUser = new UserId('550e8400-e29b-41d4-a716-446655440000');

        $dto = $this->handler->handle(new TimeEntryId($id), $currentUser);

        return $this->respondWithData($dto);
    }
}
