<?php

declare(strict_types=1);

namespace App\Http\Actions\TimeEntry;

use App\Application\Commands\TimeEntry\TimeEntryCommand;
use App\Application\Handlers\TimeEntry\AddTimeEntryHandler;
use App\Application\Validators\TimeEntryValidator;
use App\Domain\Enums\Permission;
use App\Domain\Exception\ForbiddenException;
use App\Domain\Exception\ValidationException;
use App\Http\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class AddTimeEntryAction extends Action {
    public function __construct(LoggerInterface $logger, private AddTimeEntryHandler $handler) {
        parent::__construct($logger);
    }

    protected function action() : Response {
        $data = $this->getFormData();

        $errors = TimeEntryValidator::validate($data);
        if (count($errors) > 0) {
            throw new ValidationException('Felaktig indata', $errors);
        }

        $currentUser = $this->getCurrentUser();

        if (
            $currentUser->getUserId()->toString() !== $data['userId']
            && !$currentUser->hasPermission(Permission::ADD_ENTRY)
        ) {
            throw new ForbiddenException(
                'Saknar behörighet att registrera tid för annan användare',
            );
        }

        $command = TimeEntryCommand::fromRequest($data);

        $dto = $this->handler->handle($command);

        return $this->respondWithData($dto, 201, );
    }
}
