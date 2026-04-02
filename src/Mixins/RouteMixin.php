<?php

declare(strict_types=1);

namespace Foodticket\Wolt\Mixins;

use Closure;
use Foodticket\Wolt\Controllers\OAuthController;
use Foodticket\Wolt\Controllers\WebhookController;

/**
 * @mixin \Illuminate\Routing\Router
 */
class RouteMixin
{
    public function woltWebhooks(): Closure
    {
        return function (string $uri = 'wolt/orders') {
            /** @var \Illuminate\Routing\Router $this */
            return $this->post($uri, [WebhookController::class, 'handle'])
                ->name('wolt.webhooks');
        };
    }

    public function woltOAuthCallback(): Closure
    {
        return function (string $uri = 'wolt/oauth/callback') {
            /** @var \Illuminate\Routing\Router $this */
            return $this->get($uri, [OAuthController::class, 'handle'])
                ->name('wolt.oauth.callback');
        };
    }
}
