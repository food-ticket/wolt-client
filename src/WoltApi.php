<?php

declare(strict_types=1);

namespace Foodticket\Wolt;

use Foodticket\Wolt\Endpoints\ManagesMenu;
use Foodticket\Wolt\Endpoints\ManagesOrders;
use Foodticket\Wolt\Endpoints\ManagesVenues;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class WoltApi
{
    use ManagesMenu;
    use ManagesOrders;
    use ManagesVenues;

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
     * @throws ConnectionException
     */
    private function request(): PendingRequest
    {
        return $this->requestWithToken($this->oauthClient->getAccessToken());
    }

    private function requestWithToken(string $accessToken): PendingRequest
    {
        return Http::baseUrl($this->getBaseUrl())
            ->asJson()
            ->withToken($accessToken);
    }
}
