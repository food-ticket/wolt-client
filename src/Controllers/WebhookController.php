<?php

declare(strict_types=1);

namespace Foodticket\Wolt\Controllers;

use Foodticket\Wolt\WebhookSignature;
use Foodticket\Wolt\WoltWebhook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Event;

class WebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $secret = (string) config('wolt.client_secret');
//        $verifier = new WebhookSignature($secret);
//
//        if (! $verifier->verify($request->getContent(), $request->header(WebhookSignature::HEADER))) {
//            return response()->json(['status' => 'invalid_signature'], 401);
//        }

        $webhook = new WoltWebhook($request->json()->all(), $request->headers->all());

        Event::dispatch($webhook->eventName(), $webhook);

        return response()->json(['status' => 'ok']);
    }
}
