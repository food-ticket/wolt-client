<?php

declare(strict_types=1);

namespace Foodticket\Wolt;

use Illuminate\Support\Arr;

class WoltWebhook
{
    public function __construct(
        private readonly array $payload,
        private readonly array $headers = [],
    ) {
        //
    }

    public function eventName(): string
    {
        return 'wolt-webhooks.order_notification';
    }

    public function orderId(): string
    {
        return Arr::get($this->payload, 'id');
    }

    public function venueId(): ?string
    {
        return Arr::get($this->payload, 'venue.external_venue_id');
    }

    public function payload(): array
    {
        return $this->payload;
    }

    public function headers(): array
    {
        return $this->headers;
    }
}
