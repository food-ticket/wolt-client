<?php

declare(strict_types=1);

namespace Foodticket\Wolt;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class WoltApi
{
    public function __construct(private readonly WoltOauthClient $oauthClient)
    {
        //
    }

    public function getBaseUrl(): string
    {
        return app()->isProduction()
            ? 'https://pos-integration-service.wolt.com'
            : 'https://pos-integration-service.development.dev.woltapi.com';
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function getOrder(string $orderId): ?array
    {
        $response = $this->request()->get("/v1/orders/{$orderId}");

        if ($response->successful()) {
            return $response->json();
        }

        $response->throw();

        return null;
    }

    /**
     * @throws ConnectionException
     * @throws RequestException
     */
    public function acceptOrder(string $orderId): Response
    {
        return $this->request()->put("/v1/orders/{$orderId}/accept");
    }

    /**
     * @throws ConnectionException
     * @throws RequestException
     */
    public function rejectOrder(string $orderId): Response
    {
        return $this->request()->put("/v1/orders/{$orderId}/reject");
    }

    /**
     * @throws ConnectionException
     * @throws RequestException
     */
    public function markOrderReady(string $orderId): Response
    {
        return $this->request()->put("/v1/orders/{$orderId}/ready");
    }

    /**
     * @throws ConnectionException
     * @throws RequestException
     */
    public function markOrderDelivered(string $orderId): Response
    {
        return $this->request()->put("/v1/orders/{$orderId}/delivered");
    }

    /**
     * @throws ConnectionException
     */
    private function request(): PendingRequest
    {
        return Http::baseUrl($this->getBaseUrl())
            ->asJson()
            ->withToken($this->oauthClient->getAccessToken());
    }
}
