<?php

declare(strict_types=1);

namespace Foodticket\Wolt\Controllers;

use Exception;
use Foodticket\Wolt\Events\OAuthCallbackReceived;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Event;

class OAuthController extends Controller
{
    /**
     * Handle the OAuth redirect callback from Wolt.
     *
     * Fires OAuthCallbackReceived so the consuming application can exchange
     * the code for a token and store it. The `state` parameter can be used
     * to identify which venue/client the authorisation belongs to.
     *
     * @throws Exception
     */
    public function handle(Request $request): Response
    {
        if ($request->has('error')) {
            throw new Exception('Wolt OAuth error: '.$request->input('error'));
        }

        if (! $request->has(['code', 'state'])) {
            throw new Exception('Wolt OAuth callback is missing required parameters.');
        }

        Event::dispatch(new OAuthCallbackReceived(
            code: $request->input('code'),
            state: $request->input('state'),
        ));

        return response()->noContent();
    }
}
