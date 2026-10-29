<?php

declare(strict_types=1);

use App\Http\Actions\Health\ShowHealthAction;
use App\Http\Actions\User\UpsertUserAction;
use Slim\App;

return function (App $app) : void {
    $app->get('/health', ShowHealthAction::class);

    // Användarhantering
    $app->post('/users', UpsertUserAction::class);
    /*    $app->get('/users', GetAllUsersAction::class);
        $app->get('/users/{id}', GetUserAction::class);
        $app->delete('/users/{id}', DeleteUserAction::class);
    */

};
