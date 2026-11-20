<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Actions\TimeEntry;

use App\Application\Handlers\TimeEntry\SearchTimeEntriesHandler;
use App\Application\Security\CurrentUser;
use App\Domain\DataTransportObjects\SearchTimeEntriesDTO;
use App\Domain\Enums\Permission;
use App\Domain\Exception\ForbiddenException;
use App\Domain\Exception\ValidationException;
use App\Domain\ValueObjects\UserId;
use App\Http\Actions\TimeEntry\SearchTimeEntriesAction;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

final class SearchTimeEntriesActionTest extends TestCase {
    public function testReturnsSearchResult() : void {
        $logger = $this->createMock(
            LoggerInterface::class,
        );

        $handler = $this->createMock(
            SearchTimeEntriesHandler::class,
        );

        $handler
            ->expects($this->once())
            ->method('handle')
            ->willReturn(
                new SearchTimeEntriesDTO(0, [], 0, ),
            );

        $action = new SearchTimeEntriesAction($logger, $handler, );

        $currentUser = $this->createCurrentUser();

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/time-entry/search', )
            ->withAttribute('currentUser', $currentUser, )
            ->withParsedBody([]);

        $response = (new ResponseFactory())
            ->createResponse();

        $result = $action($request, $response, [], );

        self::assertSame(200, $result->getStatusCode(), );
    }

    public function testThrowsForbiddenExceptionWhenUserLacksViewPermission() : void {
        $logger = $this->createMock(LoggerInterface::class, );

        $handler = $this->createMock(SearchTimeEntriesHandler::class, );

        $handler
            ->expects($this->never())
            ->method('handle');

        $action = new SearchTimeEntriesAction($logger, $handler, );

        $currentUser = new CurrentUser(
            new UserId('550e8400-e29b-41d4-a716-446655440000', ),
            [],
            [],
        );

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/time-entry/search', )
            ->withAttribute('currentUser', $currentUser, )
            ->withParsedBody([]);

        $response = (new ResponseFactory())
            ->createResponse();

        $this->expectException(ForbiddenException::class, );

        $action($request, $response, [], );
    }

    public function testThrowsValidationExceptionWhenDataIsInvalid() : void {
        $logger = $this->createMock(LoggerInterface::class, );

        $handler = $this->createMock(SearchTimeEntriesHandler::class, );

        $handler
            ->expects($this->never())
            ->method('handle');

        $action = new SearchTimeEntriesAction($logger, $handler, );

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/time-entry/search', )
            ->withAttribute('currentUser', $this->createCurrentUser(), )
            ->withParsedBody(['users' => [],            ]);

        $response = (new ResponseFactory())
            ->createResponse();

        $this->expectException(ValidationException::class, );

        $action($request, $response, [], );
    }

    private function createCurrentUser() : CurrentUser {
        return new CurrentUser(
            new UserId('550e8400-e29b-41d4-a716-446655440000', ),
            [],
            [Permission::VIEW->value,            ],
        );
    }
}
