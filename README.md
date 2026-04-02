# Wolt Client

A PHP client to integrate with the [Wolt](https://wolt.com) POS Integration API for Laravel.

## Installation

```bash
composer require foodticket/wolt
```

The service provider is auto-discovered and registers itself automatically.

Publish the config file:

```bash
php artisan vendor:publish --tag=wolt-config
```

## Configuration

Add the following variables to your `.env` file:

```env
WOLT_CLIENT_ID=
WOLT_CLIENT_SECRET=
WOLT_WEBHOOK_SECRET=
WOLT_REDIRECT_URI=https://your-app.com/wolt/oauth/callback
```

## Routes

Register the Wolt routes in your `RouteServiceProvider`:

```php
use Illuminate\Support\Facades\Route;

Route::woltWebhooks();       // POST /wolt/orders
Route::woltOAuthCallback();  // GET  /wolt/oauth/callback
```

Both macros accept an optional URI argument to override the default path:

```php
Route::woltWebhooks('custom/wolt/orders');
Route::woltOAuthCallback('custom/wolt/oauth/callback');
```

## Webhooks

The webhook endpoint accepts incoming order notifications from Wolt and dispatches a `wolt-webhooks.order_notification` Laravel event with a `WoltWebhook` instance. Register a listener to handle it:

```php
use Foodticket\Wolt\WoltWebhook;

Event::listen('wolt-webhooks.order_notification', function (WoltWebhook $webhook) {
    // $webhook->orderId()
    // $webhook->venueId()
    // $webhook->payload()
});
```

## OAuth

The OAuth callback endpoint handles the authorization code redirect from Wolt and dispatches an `OAuthCallbackReceived` event. Register a listener to exchange the code for tokens:

```php
use Foodticket\Wolt\Events\OAuthCallbackReceived;

Event::listen(OAuthCallbackReceived::class, function (OAuthCallbackReceived $event) {
    // $event->code  — authorization code from Wolt
    // $event->state — state parameter set when initiating the OAuth flow
});
```

To exchange the authorization code for tokens, inject `WoltOauthClient`:

```php
use Foodticket\Wolt\WoltOauthClient;

$tokens = app(WoltOauthClient::class)->exchangeCode($event->code);
// $tokens['access_token']
// $tokens['refresh_token']
// $tokens['expires_in']
```

When initiating the OAuth flow, encode identifying information in the `state` parameter so the callback listener can resolve the correct resource:

```php
use Illuminate\Support\Facades\Crypt;

$state = Crypt::encryptString($client->uuid);
$redirectUrl = 'https://pos.wolt.com/oauth/authorize?client_id='.config('wolt.client_id').'&state='.$state.'&redirect_uri='.urlencode(config('wolt.redirect_uri'));
```

## API

Inject `WoltApi` to interact with the Wolt POS Integration API directly:

```php
use Foodticket\Wolt\WoltApi;

$api = app(WoltApi::class);

$api->getOrder($orderId);
$api->acceptOrder($orderId);
$api->rejectOrder($orderId);
$api->markOrderReady($orderId);
$api->markOrderDelivered($orderId);
```
