<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Actions\TimeEntry;

use App\Application\Handlers\TimeEntry\ExportTimeEntriesHandler;
use App\Application\Security\CurrentUser;
use App\Domain\Enums\Permission;
use App\Domain\Exception\ForbiddenException;
use App\Domain\Exception\ValidationException;
use App\Domain\ValueObjects\UserId;
use App\Http\Actions\TimeEntry\ExportTimeEntriesAction;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

final class ExportTimeEntriesActionTest extends TestCase {
    public function testExportsTimeEntriesAndReturnsFile() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $handler = $this->createMock(ExportTimeEntriesHandler::class);

        $handler
            ->expects($this->once())
            ->method('handle')
            ->willReturn("UserId;Date\n123;2026-11-18\n");

        $action = new ExportTimeEntriesAction($logger, $handler, );

        $currentUser = new CurrentUser(
            new UserId('550e8400-e29b-41d4-a716-446655440000', ),
            [],
            [Permission::EXPORT->value, ],
        );

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/time-entry/export')
            ->withAttribute('currentUser', $currentUser)
            ->withParsedBody([]);

        $response = (new ResponseFactory())
            ->createResponse();

        $result = $action($request, $response, []);

        self::assertSame(200, $result->getStatusCode());

        self::assertSame('attachment', strtok($result->getHeaderLine('Content-Disposition'), ';'));
    }

    public function testThrowsForbiddenExceptionWhenUserLacksPermission() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $handler = $this->createMock(ExportTimeEntriesHandler::class);

        $handler
            ->expects($this->never())
            ->method('handle');

        $action = new ExportTimeEntriesAction($logger, $handler, );

        $currentUser = new CurrentUser(
            new UserId('550e8400-e29b-41d4-a716-446655440000', ),
            [],
            [],
        );

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/time-entry/export')
            ->withAttribute('currentUser', $currentUser)
            ->withParsedBody([]);

        $response = (new ResponseFactory())
            ->createResponse();

        $this->expectException(ForbiddenException::class);

        $this->expectExceptionMessage('Saknar exportbehörighet');

        $action($request, $response, []);
    }

    public function testThrowsValidationExceptionWhenUsersArrayIsEmpty() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $handler = $this->createMock(ExportTimeEntriesHandler::class);

        $handler
            ->expects($this->never())
            ->method('handle');

        $action = new ExportTimeEntriesAction($logger, $handler, );

        $currentUser = new CurrentUser(
            new UserId('550e8400-e29b-41d4-a716-446655440000', ),
            [],
            [Permission::EXPORT->value, ],
        );

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/time-entry/export')
            ->withAttribute('currentUser', $currentUser)
            ->withParsedBody(['users' => [], ]);

        $response = (new ResponseFactory())
            ->createResponse();

        $this->expectException(ValidationException::class);

        $action(
            $request,
            $response,
            [],
        );
    }
}
