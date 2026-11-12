<?php

declare(strict_types=1);

namespace App\Http\Actions\TimeEntry;

use App\Application\Handlers\TimeEntry\ApproveTimeEntryHandler;
use App\Domain\Exception\ForbiddenException;
use App\Domain\ValueObjects\TimeEntryId;
use App\Http\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class ApproveTimeEntryAction extends Action {
    public function __construct(LoggerInterface $logger, private ApproveTimeEntryHandler $handler) {
        parent::__construct($logger);
    }

    protected function action() : Response {
        $id = $this->request->getAttribute('id');
        // Hämta currentUser från AuthenticationMiddleware
        $currentUser = $this->getCurrentUser();

        if (!$currentUser->hasPermission('time.approve')) {
            throw new ForbiddenException(
                'Saknar attestbehörighet',
            );
        }
        $dto = $this->handler->handle(new TimeEntryId($id), $currentUser);

        return $this->respondWithData($dto);
    }
}
