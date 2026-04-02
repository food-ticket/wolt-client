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

    public function getTokenUrl(): string
    {
        return app()->isProduction()
            ? 'https://authentication.wolt.com/v1/wauth2/access_token'
            : 'https://authentication.development.dev.woltapi.com/v1/wauth2/access_token';
    }

    /**
     * Exchange an authorization code for access and refresh tokens.
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    public function exchangeCode(string $code): array
    {
        $response = Http::asForm()->post($this->getTokenUrl(), [
            'grant_type' => 'authorization_code',
            'client_id' => config('wolt.client_id'),
            'client_secret' => config('wolt.client_secret'),
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
        $response = Http::asForm()->post($this->getTokenUrl(), [
            'grant_type' => 'refresh_token',
            'client_id' => config('wolt.client_id'),
            'client_secret' => config('wolt.client_secret'),
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

        $response = Http::asForm()->post($this->getTokenUrl(), [
            'grant_type' => 'client_credentials',
            'client_id' => config('wolt.client_id'),
            'client_secret' => config('wolt.client_secret'),
        ]);

        $response->throw();

        $data = $response->json();

        Cache::put(self::CACHE_KEY, $data['access_token'], now()->addSeconds($data['expires_in'] - 30));

        return $data['access_token'];
    }
}
