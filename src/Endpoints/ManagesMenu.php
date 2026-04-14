<?php

declare(strict_types=1);

namespace Foodticket\Wolt\Endpoints;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;

trait ManagesMenu
{
    /**
     * Create or replace the menu for a venue.
     *
     * @throws RequestException
     * @throws ConnectionException
     */
    public function createMenu(string $venueId, array $menu): array
    {
        return $this->request()
            ->post("/v1/restaurants/{$venueId}/menu", $menu)
            ->throw()
            ->json();
    }

    /**
     * Retrieve the current menu for a venue.
     *
     * @throws RequestException
     * @throws ConnectionException
     */
    public function getMenu(string $venueId): array
    {
        return $this->request()
            ->get("/v2/venues/{$venueId}/menu")
            ->throw()
            ->json();
    }

    /**
     * Update inventory/stock levels for menu items.
     *
     * @throws ConnectionException
     */
    public function updateItemInventory(string $venueId, $data): Response
    {
        return $this->request()
            ->patch("/venues/{$venueId}/items/inventory", $data);
    }

    /**
     * Update item details (pricing, availability, enabled status).
     *
     * @throws ConnectionException
     */
    public function updateItems(string $venueId, $data): Response
    {
        return $this->request()
            ->patch("/venues/{$venueId}/items", $data);
    }

    /**
     * Update option values and configurations.
     *
     * @throws ConnectionException
     */
    public function updateOptions(string $venueId, $data): Response
    {
        return $this->request()
            ->patch("/venues/{$venueId}/options/values", $data);
    }
}
