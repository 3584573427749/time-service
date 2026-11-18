<?php

declare(strict_types=1);

namespace App\Http\Actions\TimeEntry;

use App\Application\Commands\TimeEntry\ExportTimeEntriesCommand;
use App\Application\Handlers\TimeEntry\ExportTimeEntriesHandler;
use App\Application\Validators\ExportTimeEntriesValidator;
use App\Domain\Enums\Permission;
use App\Domain\Exception\ForbiddenException;
use App\Domain\Exception\ValidationException;
use App\Http\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class ExportTimeEntriesAction extends Action {
    public function __construct(LoggerInterface $logger, private ExportTimeEntriesHandler $handler) {
        parent::__construct($logger);
    }

    protected function action() : Response {
        $currentUser = $this->getCurrentUser();

        if (!$currentUser->hasPermission(Permission::EXPORT)) {
            throw new ForbiddenException(
                'Saknar exportbehörighet',
            );
        }

        $data = $this->getFormData();

        $errors = ExportTimeEntriesValidator::validate($data);

        if (count($errors) > 0) {
            throw new ValidationException('Felaktig indata', $errors, );
        }

        $command = ExportTimeEntriesCommand::fromRequest($data);

        $csv = $this->handler->handle(
            $command,
            $currentUser,
        );
        $fileName = 'TimeEntriesExport_' . date('YmdHis') . 'csv';

        return $this->respondWithDownloadableTextFile($csv, $fileName);
    }
}
