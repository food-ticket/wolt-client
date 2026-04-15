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
    public function getOrder(string $orderId): array
    {
        return $this->request()
            ->get("/orders/{$orderId}")
            ->throw()
            ->json();
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function getOrderV2(string $orderId): array
    {
        return $this->request()
            ->get("/v2/orders/{$orderId}")
            ->throw()
            ->json();
    }

    /**
     * @throws ConnectionException
     */
    public function acceptOrder(string $orderId, $data = null): Response
    {
        return $this->request()
            ->put("/orders/{$orderId}/accept", $data);
    }

    /**
     * @throws ConnectionException
     */
    public function acceptSelfDeliveryOrder(string $orderId, $data = null): Response
    {
        return $this->request()
            ->put("/orders/{$orderId}/self-delivery/accept", $data);
    }

    /**
     * @throws ConnectionException
     */
    public function rejectOrder(string $orderId, $data = null): Response
    {
        return $this->request()
            ->put("/orders/{$orderId}/reject", $data);
    }

    /**
     * @throws ConnectionException
     */
    public function markOrderReady(string $orderId, $data = null): Response
    {
        return $this->request()
            ->put("/orders/{$orderId}/ready", $data);
    }

    /**
     * @throws ConnectionException
     */
    public function markPickupCompleted(string $orderId, $data = null): Response
    {
        return $this->request()
            ->put("/orders/{$orderId}/pickup-completed", $data);
    }

    /**
     * @throws ConnectionException
     */
    public function markCourierAtCustomer(string $orderId, $data = null): Response
    {
        return $this->request()
            ->put("/orders/{$orderId}/courier-at-customer", $data);
    }

    /**
     * @throws ConnectionException
     */
    public function markOrderDelivered(string $orderId, $data = null): Response
    {
        return $this->request()
            ->put("/orders/{$orderId}/delivered", $data);
    }

    /**
     * @throws ConnectionException
     */
    public function confirmPreorder(string $orderId, $data = null): Response
    {
        return $this->request()
            ->put("/orders/{$orderId}/confirm-preorder", $data);
    }

    /**
     * @throws ConnectionException
     */
    public function replaceItems(string $orderId, array $itemChanges = [], array $itemAdditions = []): Response
    {
        return $this->request()
            ->put("/orders/{$orderId}/replace-items", array_filter([
                'item_changes' => $itemChanges,
                'item_additions' => $itemAdditions,
            ]));
    }

    /**
     * @throws ConnectionException
     */
    public function markSentToPos(string $orderId, $data = null): Response
    {
        return $this->request()
            ->put("/orders/{$orderId}/sent-to-pos", $data);
    }

    /**
     * @throws ConnectionException
     */
    public function markDepositsReturned(string $orderId, $data = null): Response
    {
        return $this->request()
            ->put("/orders/{$orderId}/deposits-returned", $data);
    }

    /**
     * @throws ConnectionException
     */
    public function refundItems(string $orderId, $data): Response
    {
        return $this->request()
            ->post("/orders/{$orderId}/refund-items", $data);
    }

    /**
     * @throws ConnectionException
     */
    public function refundBasket(string $orderId, $data): Response
    {
        return $this->request()
            ->post("/orders/{$orderId}/refund-basket", $data);
    }

    /**
     * @throws ConnectionException
     */
    public function updateDeliveryLocation(string $orderId, $location): Response
    {
        return $this->request()
            ->put("/orders/{$orderId}/delivery/tracking/location", $location);
    }

    /**
     * @throws ConnectionException
     */
    public function updateDeliveryEta(string $orderId, $data): Response
    {
        return $this->request()
            ->put("/orders/{$orderId}/delivery/eta", $data);
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function getDocumentUploadLink(string $orderId, string $documentType): array
    {
        return $this->request()
            ->post("/orders/{$orderId}/documents/{$documentType}/upload-links")
            ->throw()
            ->json();
    }
}
