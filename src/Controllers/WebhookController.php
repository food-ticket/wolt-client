<?php

declare(strict_types=1);

namespace Foodticket\Wolt\Controllers;

use Foodticket\Wolt\WoltWebhook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Event;

class WebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $webhook = new WoltWebhook($request->json()->all(), $request->headers->all());

        Event::dispatch($webhook->eventName(), $webhook);

        return response()->json(['status' => 'ok']);
    }
}
