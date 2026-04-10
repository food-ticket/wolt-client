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
     * @throws RequestException
     * @throws ConnectionException
     */
    public function acceptOrder(string $orderId): Response
    {
        return $this->request()
            ->put("/orders/{$orderId}/accept")
            ->throw();
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function acceptSelfDeliveryOrder(string $orderId): Response
    {
        return $this->request()
            ->put("/orders/{$orderId}/self-delivery/accept")
            ->throw();
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function rejectOrder(string $orderId): Response
    {
        return $this->request()
            ->put("/orders/{$orderId}/reject")
            ->throw();
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function markOrderReady(string $orderId): Response
    {
        return $this->request()
            ->put("/orders/{$orderId}/ready")
            ->throw();
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function markPickupCompleted(string $orderId): Response
    {
        return $this->request()
            ->put("/orders/{$orderId}/pickup-completed")
            ->throw();
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function markCourierAtCustomer(string $orderId): Response
    {
        return $this->request()
            ->put("/orders/{$orderId}/courier-at-customer")
            ->throw();
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function markOrderDelivered(string $orderId): Response
    {
        return $this->request()
            ->put("/orders/{$orderId}/delivered")
            ->throw();
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function confirmPreorder(string $orderId): Response
    {
        return $this->request()
            ->put("/orders/{$orderId}/confirm-preorder")
            ->throw();
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function replaceItems(string $orderId, array $itemChanges = [], array $itemAdditions = []): Response
    {
        return $this->request()
            ->put("/orders/{$orderId}/replace-items", array_filter([
                'item_changes' => $itemChanges,
                'item_additions' => $itemAdditions,
            ]))
            ->throw();
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function markSentToPos(string $orderId): Response
    {
        return $this->request()
            ->put("/orders/{$orderId}/sent-to-pos")
            ->throw();
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function markDepositsReturned(string $orderId): Response
    {
        return $this->request()
            ->put("/orders/{$orderId}/deposits-returned")
            ->throw();
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function refundItems(string $orderId, array $data): Response
    {
        return $this->request()
            ->post("/orders/{$orderId}/refund-items", $data)
            ->throw();
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function refundBasket(string $orderId, array $data): Response
    {
        return $this->request()
            ->post("/orders/{$orderId}/refund-basket", $data)
            ->throw();
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function updateDeliveryLocation(string $orderId, array $location): Response
    {
        return $this->request()
            ->put("/orders/{$orderId}/delivery/tracking/location", $location)
            ->throw();
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function updateDeliveryEta(string $orderId, array $data): Response
    {
        return $this->request()
            ->put("/orders/{$orderId}/delivery/eta", $data)
            ->throw();
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
