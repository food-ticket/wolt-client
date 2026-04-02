<?php

declare(strict_types=1);

namespace Foodticket\Wolt\Endpoints;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;

trait ManagesOrders
{
    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function getOrder(string $orderId): ?array
    {
        $response = $this->request()->get("/v1/orders/{$orderId}");

        if ($response->successful()) {
            return $response->json();
        }

        $response->throw();

        return null;
    }

    /**
     * @throws ConnectionException
     * @throws RequestException
     */
    public function acceptOrder(string $orderId): Response
    {
        return $this->request()->put("/v1/orders/{$orderId}/accept");
    }

    /**
     * @throws ConnectionException
     * @throws RequestException
     */
    public function rejectOrder(string $orderId): Response
    {
        return $this->request()->put("/v1/orders/{$orderId}/reject");
    }

    /**
     * @throws ConnectionException
     * @throws RequestException
     */
    public function markOrderReady(string $orderId): Response
    {
        return $this->request()->put("/v1/orders/{$orderId}/ready");
    }

    /**
     * @throws ConnectionException
     * @throws RequestException
     */
    public function markOrderDelivered(string $orderId): Response
    {
        return $this->request()->put("/v1/orders/{$orderId}/delivered");
    }
}
