<?php

declare(strict_types=1);

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
    $app->post('/users', UpsertUserAction::class);
    $app->get('/users', GetAllUsersAction::class);
    $app->get('/users/{id}', GetUserAction::class);
    $app->delete('/users/{id}', DeleteUserAction::class);

    $app->post('/time-entry', AddTimeEntryAction::class);
    $app->get('/time-entry/{id}', GetTimeEntryAction::class);
    $app->put('/time-entry/{id}', UpdateTimeEntryAction::class);
    $app->delete('/time-entry/{id}', DeleteTimeEntryAction::class);

    $app->put('/time-entry/{id}/approve', ApproveTimeEntryAction::class);

};
