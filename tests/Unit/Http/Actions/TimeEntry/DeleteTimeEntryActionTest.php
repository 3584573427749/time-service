<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Actions\TimeEntry;

use App\Application\Handlers\TimeEntry\DeleteTimeEntryHandler;
use App\Domain\ValueObjects\TimeEntryId;
use App\Http\Actions\TimeEntry\DeleteTimeEntryAction;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

final class DeleteTimeEntryActionTest extends TestCase {
    public function testDeletesTimeEntryAndReturns204() : void {
        $logger = $this->createMock(
            LoggerInterface::class,
        );

        $handler = $this->createMock(
            DeleteTimeEntryHandler::class,
        );

        $handler
            ->expects($this->once())
            ->method('handle')
            ->with(
                $this->isInstanceOf(
                    TimeEntryId::class,
                ),
            );

        $action = new DeleteTimeEntryAction(
            $logger,
            $handler,
        );

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'DELETE',
                '/time-entry/550e8400-e29b-41d4-a716-446655440001',
            )
            ->withAttribute(
                'id',
                '550e8400-e29b-41d4-a716-446655440001',
            );

        $response = (new ResponseFactory())
            ->createResponse();

        $result = $action(
            $request,
            $response,
            [],
        );

        self::assertSame(
            204,
            $result->getStatusCode(),
        );
    }
}
