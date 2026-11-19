<?php

declare(strict_types=1);

namespace App\Http\Actions\TimeEntry;

use App\Application\Commands\TimeEntry\SearchTimeEntriesCommand;
use App\Application\Handlers\TimeEntry\SearchTimeEntriesHandler;
use App\Application\Validators\SearchTimeEntriesValidator;
use App\Domain\Enums\Permission;
use App\Domain\Exception\ForbiddenException;
use App\Domain\Exception\ValidationException;
use App\Http\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class SearchTimeEntriesAction extends Action {
    public function __construct(LoggerInterface $logger, private SearchTimeEntriesHandler $handler) {
        parent::__construct($logger);
    }

    protected function action() : Response {
        $currentUser = $this->getCurrentUser();

        if (!$currentUser->hasPermission(Permission::VIEW)) {
            throw new ForbiddenException('Saknar behörighet att söka tidsposter', );
        }

        $data = $this->getFormData();

        $data = $this->getFormData();

        $errors = SearchTimeEntriesValidator::validate($data);

        if (count($errors) > 0) {
            throw new ValidationException('Felaktig indata', $errors, );
        }

        $command = SearchTimeEntriesCommand::fromRequest($data);

        $result = $this->handler->handle($command);

        return $this->respondWithData($result);
    }
}
