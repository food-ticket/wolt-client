<?php

declare(strict_types=1);

namespace Foodticket\Wolt;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WoltOauthClient
{
    private const string CACHE_KEY = 'api:wolt:ssio:accessToken';

    private const array TOKEN_URLS = [
        'production' => 'https://authentication.wolt.com/v1/wauth2/access_token',
        'test' => 'https://integrations-authentication-service.development.dev.woltapi.com/oauth2/token',
    ];

    public function getTokenUrl(): string
    {
        return config('wolt.token_url') ?? self::TOKEN_URLS[config('wolt.environment', 'production')];
    }

    /**
     * Exchange an authorization code for access and refresh tokens.
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    public function exchangeCode(string $code): array
    {
        $response = Http::asForm()
            ->withBasicAuth(config('wolt.client_id'), config('wolt.client_secret'))
            ->post($this->getTokenUrl(), [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => config('wolt.redirect_uri'),
            ]);

        $response->throw();

        return $response->json();
    }

    /**
     * Exchange a refresh token for a new access token.
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    public function refreshAccessToken(string $refreshToken): array
    {
        $response = Http::asForm()
            ->withBasicAuth(config('wolt.client_id'), config('wolt.client_secret'))
            ->post($this->getTokenUrl(), [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ]);

        $response->throw();

        return $response->json();
    }

    /**
     * @throws ConnectionException
     * @throws RequestException
     */
    public function getAccessToken(): string
    {
        $cached = Cache::get(self::CACHE_KEY);

        if ($cached) {
            return $cached;
        }

        $response = Http::asForm()
            ->withBasicAuth(config('wolt.client_id'), config('wolt.client_secret'))
            ->post($this->getTokenUrl(), [
                'grant_type' => 'client_credentials',
            ]);

        $response->throw();

        $data = $response->json();

        Cache::put(self::CACHE_KEY, $data['access_token'], now()->addSeconds($data['expires_in'] - 30));

        return $data['access_token'];
    }
}
