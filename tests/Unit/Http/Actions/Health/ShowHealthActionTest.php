<?php


declare(strict_types=1);

namespace Tests\Unit\Http\Actions\Health;

use App\Application\Services\HealthService;
use App\Domain\DataTransportObjects\HealthDTO;
use App\Http\Actions\Health\ShowHealthAction;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

final class ShowHealthActionTest extends TestCase {
    public function testReturnsHealthPayload() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $dto = new HealthDTO(
            'ok',
            'time-service',
            '1.0.0',
            [
                'database' => 'ok',
            ],
        );

        $healthService = $this->createMock(HealthService::class);

        $healthService
            ->expects($this->once())
            ->method('getHealth')
            ->willReturn($dto);

        $action = new ShowHealthAction(
            $logger,
            $healthService,
        );

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'GET',
                '/health',
            );

        $response = (new ResponseFactory())
            ->createResponse();

        $result = $action(
            $request,
            $response,
            [],
        );

        self::assertSame(
            200,
            $result->getStatusCode(),
        );

        $payload = json_decode(
            (string)$result->getBody(),
            true,
        );

        self::assertIsArray($payload);
        self::assertSame('ok', $payload['data']['status']);
        self::assertSame('time-service', $payload['data']['service']);
        self::assertSame('ok', $payload['data']['checks']['database']);
    }
}
