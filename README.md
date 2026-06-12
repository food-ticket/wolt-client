# Wolt Client

A PHP client to integrate with the [Wolt](https://wolt.com) POS Integration API for Laravel.

## Requirements

- PHP >= 8.3
- Laravel >= 12.0

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
```

### Orders

#### `getOrder(string $orderId): array`
`GET /orders/{orderId}`

Fetch full order details (v1).

```php
$order = $api->getOrder($orderId);
```

#### `getOrderV2(string $orderId): array`
`GET /v2/orders/{orderId}`

Fetch full order details (v2). Throws `RequestException` on non-2xx responses.

```php
$order = $api->getOrderV2($orderId);
```

#### `acceptOrder(string $orderId, mixed $data = null): Response`
`PUT /orders/{orderId}/accept`

Accept an incoming order. Pass optional body data (e.g. estimated preparation time).

```php
$api->acceptOrder($orderId);
$api->acceptOrder($orderId, ['adjusted_preparation_time' => 15]);
```

#### `acceptSelfDeliveryOrder(string $orderId, mixed $data = null): Response`
`PUT /orders/{orderId}/self-delivery/accept`

Accept an order that uses your own delivery fleet.

```php
$api->acceptSelfDeliveryOrder($orderId);
```

#### `rejectOrder(string $orderId, mixed $data = null): Response`
`PUT /orders/{orderId}/reject`

Reject an incoming order. Pass optional body data (e.g. rejection reason).

```php
$api->rejectOrder($orderId, ['reason' => 'RESTAURANT_CLOSED']);
```

#### `markOrderReady(string $orderId, mixed $data = null): Response`
`PUT /orders/{orderId}/ready`

Signal that the order is ready for pickup by the courier.

```php
$api->markOrderReady($orderId);
```

#### `markPickupCompleted(string $orderId, mixed $data = null): Response`
`PUT /orders/{orderId}/pickup-completed`

Signal that the courier has picked up the order (self-delivery flow).

```php
$api->markPickupCompleted($orderId);
```

#### `markCourierAtCustomer(string $orderId, mixed $data = null): Response`
`PUT /orders/{orderId}/courier-at-customer`

Signal that the courier has arrived at the customer's location (self-delivery flow).

```php
$api->markCourierAtCustomer($orderId);
```

#### `markOrderDelivered(string $orderId, mixed $data = null): Response`
`PUT /orders/{orderId}/delivered`

Mark the order as successfully delivered (self-delivery flow).

```php
$api->markOrderDelivered($orderId);
```

#### `confirmPreorder(string $orderId, mixed $data = null): Response`
`PUT /orders/{orderId}/confirm-preorder`

Confirm a scheduled/pre-order so Wolt knows the venue will fulfil it.

```php
$api->confirmPreorder($orderId);
```

#### `replaceItems(string $orderId, array $itemChanges = [], array $itemAdditions = []): Response`
`PUT /orders/{orderId}/replace-items`

Modify an accepted order — change quantities on existing items or add new ones. Empty arrays are omitted from the request body.

```php
$api->replaceItems(
    $orderId,
    itemChanges: [
        ['item_id' => 'abc', 'count' => 2],
    ],
    itemAdditions: [
        ['item_id' => 'xyz', 'count' => 1],
    ],
);
```

#### `markSentToPos(string $orderId, mixed $data = null): Response`
`PUT /orders/{orderId}/sent-to-pos`

Acknowledge that the order has been forwarded to the POS system.

```php
$api->markSentToPos($orderId);
```

#### `markDepositsReturned(string $orderId, mixed $data = null): Response`
`PUT /orders/{orderId}/deposits-returned`

Confirm that deposit items (e.g. bottles) have been returned.

```php
$api->markDepositsReturned($orderId);
```

#### `refundItems(string $orderId, mixed $data): Response`
`POST /orders/{orderId}/refund-items`

Issue a partial refund for specific items in an order.

```php
$api->refundItems($orderId, [
    'items' => [
        ['item_id' => 'abc', 'count' => 1],
    ],
]);
```

#### `refundBasket(string $orderId, mixed $data): Response`
`POST /orders/{orderId}/refund-basket`

Issue a full basket refund for an order.

```php
$api->refundBasket($orderId, ['reason' => 'QUALITY_ISSUE']);
```

#### `updateDeliveryLocation(string $orderId, mixed $location): Response`
`PUT /orders/{orderId}/delivery/tracking/location`

Push a live GPS coordinate update for the courier during self-delivery.

```php
$api->updateDeliveryLocation($orderId, [
    'latitude'  => 52.3731,
    'longitude' => 4.8922,
]);
```

#### `updateDeliveryEta(string $orderId, mixed $data): Response`
`PUT /orders/{orderId}/delivery/eta`

Update the estimated delivery time for a self-delivery order.

```php
$api->updateDeliveryEta($orderId, ['eta' => '2026-04-11T12:30:00Z']);
```

#### `getDocumentUploadLink(string $orderId, string $documentType): array`
`POST /orders/{orderId}/documents/{documentType}/upload-links`

Request a pre-signed upload URL for attaching a document (e.g. invoice) to an order. `$documentType` is typically `'INVOICE'`.

```php
$link = $api->getDocumentUploadLink($orderId, 'INVOICE');
// $link['upload_url']
```

---

### Venues

#### `getVenueStatus(string $venueId): array`
`GET /venues/{venueId}/status`

Retrieve the current online/offline status and related details for a venue.

```php
$status = $api->getVenueStatus($venueId);
```

#### `getDeliveryProvider(string $venueId): array`
`GET /venues/{venueId}/delivery-provider`

Get the active delivery provider for a venue (`WOLT` or `SELF_DELIVERY`).

```php
$provider = $api->getDeliveryProvider($venueId);
```

#### `updateDeliveryProvider(string $venueId, string $deliveryProvider): Response`
`PATCH /venues/{venueId}/delivery-provider`

Switch the delivery provider. Accepted values: `'WOLT'`, `'SELF_DELIVERY'`.

```php
$api->updateDeliveryProvider($venueId, 'SELF_DELIVERY');
```

#### `updateOnlineStatus(string $venueId, string $status, ?string $until = null): Response`
`PATCH /venues/{venueId}/online`

Set the venue online or offline. Pass an ISO 8601 timestamp to `$until` to schedule an automatic return to online. Accepted `$status` values: `'ONLINE'`, `'OFFLINE'`.

```php
$api->updateOnlineStatus($venueId, 'ONLINE');
$api->updateOnlineStatus($venueId, 'OFFLINE', until: '2026-04-11T08:00:00Z');
```

#### `updateOpeningTimes(string $venueId, array $availability): Response`
`PATCH /venues/{venueId}/opening-times`

Update regular weekly opening hours. The `$availability` array is sent as `{"availability": [...]}`.

```php
$api->updateOpeningTimes($venueId, [
    ['day_of_week' => 'monday', 'open' => '08:00', 'close' => '22:00'],
    // ...
]);
```

#### `setSpecialOpeningTimes(string $venueId, mixed $data): Response`
`PUT /venues/{venueId}/special-opening-times`

Set one-off opening time overrides (e.g. public holidays or special events).

```php
$api->setSpecialOpeningTimes($venueId, [
    ['date' => '2026-12-25', 'closed' => true],
]);
```

---

### Menu

#### `createMenu(string $venueId, array $menu): array`
`POST /v1/restaurants/{venueId}/menu`

Create or fully replace the menu for a venue. Returns the created menu object. Throws `RequestException` on non-2xx responses.

```php
$result = $api->createMenu($venueId, $menuArray);
```

#### `getMenu(string $venueId): array`
`GET /v2/venues/{venueId}/menu`

Retrieve the current published menu for a venue. Throws `RequestException` on non-2xx responses.

```php
$menu = $api->getMenu($venueId);
```

#### `updateItemInventory(string $venueId, mixed $data): Response`
`PATCH /venues/{venueId}/items/inventory`

Update stock/availability for one or more menu items (e.g. mark items as sold out).

```php
$api->updateItemInventory($venueId, [
    'updates' => [
        ['item_id' => 'abc', 'enabled' => false],
    ],
]);
```

#### `updateItems(string $venueId, mixed $data): Response`
`PATCH /venues/{venueId}/items`

Update item properties such as name, description, price, or enabled status.

```php
$api->updateItems($venueId, [
    'updates' => [
        ['item_id' => 'abc', 'price' => 1099],
    ],
]);
```

#### `updateOptions(string $venueId, mixed $data): Response`
`PATCH /venues/{venueId}/options/values`

Update option/modifier values and their configurations (e.g. price, availability).

```php
$api->updateOptions($venueId, [
    'updates' => [
        ['option_id' => 'xyz', 'enabled' => true],
    ],
]);
```
## Security Vulnerabilities

If you discover a security vulnerability within this project, please report this by email to [developer@foodticket.nl](mailto:developer@foodticket.nl).
