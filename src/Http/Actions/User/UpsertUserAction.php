<?php

declare(strict_types=1);

namespace App\Http\Actions\User;

use App\Application\Commands\User\UpsertUserCommand;
use App\Application\Handlers\User\UpsertUserHandler;
use App\Application\Validators\UserValidator;
use App\Domain\Exception\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class UpsertUserAction extends UserAction {
    public function __construct(LoggerInterface $logger, private UpsertUserHandler $handler) {
        parent::__construct($logger);
    }

    protected function action() : Response {
        $data = (array)$this->request->getParsedBody();

        $errors = UserValidator::validate($data);
        if (count($errors) > 0) {
            throw new ValidationException('Felaktig indata', $errors);
        }

        $userCommand = UpsertUserCommand::fromRequest($data);
        $dto = $this->handler->handle($userCommand);

        return $this->respondWithData($dto);

    }
}
