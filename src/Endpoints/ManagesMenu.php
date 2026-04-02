<?php

declare(strict_types=1);

namespace Foodticket\Wolt\Endpoints;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;

trait ManagesMenu
{
    /**
     * Retrieve the current menu for a venue.
     *
     * @throws RequestException
     * @throws ConnectionException
     */
    public function getMenu(string $venueId): array
    {
        return $this->request()
            ->get("/v1/venues/{$venueId}/menu")
            ->throw()
            ->json();
    }

    /**
     * Push a full menu to a venue, replacing the existing one.
     *
     * @throws RequestException
     * @throws ConnectionException
     */
    public function updateMenu(string $venueId, array $menu): Response
    {
        return $this->request()
            ->post("/v1/venues/{$venueId}/menu", $menu)
            ->throw();
    }

    /**
     * Update the availability of a single item.
     *
     * @throws RequestException
     * @throws ConnectionException
     */
    public function updateItemAvailability(string $venueId, string $itemId, bool $available): Response
    {
        return $this->request()
            ->put("/v1/venues/{$venueId}/items/{$itemId}", ['enabled' => $available])
            ->throw();
    }
}
