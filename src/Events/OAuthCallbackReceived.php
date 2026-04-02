<?php

declare(strict_types=1);

namespace Foodticket\Wolt\Events;

class OAuthCallbackReceived
{
    public function __construct(
        public readonly string $code,
        public readonly string $state,
    ) {
        //
    }
}