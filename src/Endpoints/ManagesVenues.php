<?php

declare(strict_types=1);

namespace Foodticket\Wolt\Endpoints;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;

trait ManagesVenues
{
    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function getVenueStatus(string $venueId): array
    {
        return $this->request()
            ->get("/venues/{$venueId}/status")
            ->throw()
            ->json();
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function getDeliveryProvider(string $venueId): array
    {
        return $this->request()
            ->get("/venues/{$venueId}/delivery-provider")
            ->throw()
            ->json();
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function updateDeliveryProvider(string $venueId, string $deliveryProvider): Response
    {
        return $this->request()
            ->patch("/venues/{$venueId}/delivery-provider", [
                'delivery_provider' => $deliveryProvider,
            ])
            ->throw();
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function updateOnlineStatus(string $venueId, string $status, ?string $until = null): Response
    {
        return $this->request()
            ->patch("/venues/{$venueId}/online", array_filter([
                'status' => $status,
                'until' => $until,
            ]))
            ->throw();
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function updateOpeningTimes(string $venueId, array $availability): Response
    {
        return $this->request()
            ->patch("/venues/{$venueId}/opening-times", [
                'availability' => $availability,
            ])
            ->throw();
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function setSpecialOpeningTimes(string $venueId, array $data): Response
    {
        return $this->request()
            ->put("/venues/{$venueId}/special-opening-times", $data)
            ->throw();
    }
}
