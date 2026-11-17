<?php

declare(strict_types=1);

use App\Application\Middleware\AuthMiddleware;
use App\Http\Actions\Health\ShowHealthAction;
use App\Http\Actions\TimeEntry\AddTimeEntryAction;
use App\Http\Actions\TimeEntry\ApproveTimeEntryAction;
use App\Http\Actions\TimeEntry\DeleteTimeEntryAction;
use App\Http\Actions\TimeEntry\GetTimeEntryAction;
use App\Http\Actions\TimeEntry\UpdateTimeEntryAction;
use App\Http\Actions\User\DeleteUserAction;
use App\Http\Actions\User\GetAllUsersAction;
use App\Http\Actions\User\GetUserAction;
use App\Http\Actions\User\UpsertUserAction;
use Slim\App;

return function (App $app) : void {
    $app->get('/health', ShowHealthAction::class);

    // Användarhantering
    $app->group('', function ($group) {
        $group->post('/users', UpsertUserAction::class);
        $group->get('/users', GetAllUsersAction::class);
        $group->get('/users/{id}', GetUserAction::class);
        $group->delete('/users/{id}', DeleteUserAction::class);

        $group->post('/time-entry', AddTimeEntryAction::class);
        $group->get('/time-entry/{id}', GetTimeEntryAction::class);
        $group->put('/time-entry/{id}', UpdateTimeEntryAction::class);
        $group->delete('/time-entry/{id}', DeleteTimeEntryAction::class);

        $group->put('/time-entry/{id}/approve', ApproveTimeEntryAction::class);
    })->add(AuthMiddleware::class);
};
