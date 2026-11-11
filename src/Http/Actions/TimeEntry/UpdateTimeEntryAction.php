<?php

declare(strict_types=1);

namespace App\Http\Actions\TimeEntry;

use App\Application\Commands\TimeEntry\TimeEntryCommand;
use App\Application\Handlers\TimeEntry\UpdateTimeEntryHandler;
use App\Application\Validators\TimeEntryValidator;
use App\Domain\Exception\ValidationException;
use App\Http\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class UpdateTimeEntryAction extends Action {
    public function __construct(LoggerInterface $logger, private UpdateTimeEntryHandler $handler) {
        parent::__construct($logger);
    }

    protected function action() : Response {
        $data = $this->getFormData();

        $data['TimeEntryId'] = $this->resolveArg('id');

        $errors = TimeEntryValidator::validate($data);
        if (count($errors) > 0) {
            throw new ValidationException(
                'Felaktig indata',
                $errors,
            );
        }

        $command = TimeEntryCommand::fromRequest($data);

        $dto = $this->handler->handle($command);

        return $this->respondWithData($dto);
    }
}
