<?php

declare(strict_types=1);

namespace App\Http\Actions\TimeEntry;

use App\Application\Validators\TimeEntryValidator;
use App\Domain\Commands\TimeEntryCommand;
use App\Domain\Exception\ValidationException;
use App\Domain\Handlers\AddTimeEntryHandler;
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

        $command = TimeEntryCommand::fromRequest($data);

        $dto = $this->handler->handle($command);

        return $this->respondWithData($dto, 201, );
    }
}
