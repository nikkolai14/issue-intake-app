<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key');

        if (! $apiKey) {
            return response()->json([
                'message' => 'API key is required',
            ], 401);
        }

        $key = ApiKey::where('key', $apiKey)->first();

        if (! $key) {
            return response()->json([
                'message' => 'Invalid API key',
            ], 401);
        }

        // Mark the API key as used
        $key->markAsUsed();

        // Authenticate the user
        auth()->login($key->user);

        return $next($request);
    }
}
