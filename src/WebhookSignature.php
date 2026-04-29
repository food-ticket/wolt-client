<?php

declare(strict_types=1);

namespace Foodticket\Wolt;

/**
 * Verifies the signature of an incoming Wolt webhook request.
 *
 * Per Wolt's documentation:
 *   - Header: `WOLT-SIGNATURE`
 *   - Algorithm: HMAC-SHA256 over the raw request body
 *   - Encoding: hexadecimal string
 *   - Secret: the merchant's Wolt client secret
 *
 * @see https://developer.wolt.com/docs/webhook
 */
final class WebhookSignature
{
    public const HEADER = 'WOLT-SIGNATURE';

    public function __construct(
        private readonly string $secret,
    ) {
        //
    }

    public function verify(string $payload, ?string $signature): bool
    {
        if (empty($this->secret) || empty($signature)) {
            return false;
        }

        $expected = hash_hmac('sha256', $payload, $this->secret);

        return hash_equals($expected, $signature);
    }
}
