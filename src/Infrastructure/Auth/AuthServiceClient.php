<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class AuthServiceClient {
    public function __construct(private Client $client) {
    }

    /**
     * @return array<string, mixed>
     * @throws GuzzleException
     */
    public function validateServiceToken(string $token, string $serviceName) : array {
        try {
            $response = $this->client->post(
                '/validate-service-token',
                [
                    'json' => [
                        'token' => $token,
                        'service' => $serviceName,
                    ],
                ],
            );

            return json_decode((string)$response->getBody(), true);
        } catch (RequestException) {
            return ['valid' => false];
        }
    }
}
