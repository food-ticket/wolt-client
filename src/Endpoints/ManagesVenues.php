<?php

declare(strict_types=1);

namespace Foodticket\Wolt\Endpoints;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

trait ManagesVenues
{
    /**
     * Retrieve the venues associated with the given per-venue access token.
     * Use this after an OAuth authorization code exchange, not with the
     * client-credentials token managed by WoltOauthClient.
     *
     * @throws RequestException
     * @throws ConnectionException
     */
    public function getVenues(string $accessToken): array
    {
        return $this->requestWithToken($accessToken)
            ->get('/v1/venues')
            ->throw()
            ->json();
    }
}
