<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ApiKeyStoreRequest;
use App\Models\ApiKey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ApiKeyController extends Controller
{
    /**
     * Display API key management page.
     */
    public function index(Request $request): Response
    {
        $apiKey = $request->user()->apiKey;

        $apiKeyData = $apiKey ? [
            'id' => $apiKey->id,
            'name' => $apiKey->name,
            'key' => substr($apiKey->key, 0, 10).'...'.substr($apiKey->key, -4),
            'last_used_at' => $apiKey->last_used_at?->diffForHumans(),
            'created_at' => $apiKey->created_at->diffForHumans(),
        ] : null;

        return Inertia::render('settings/api-key', [
            'apiKey' => $apiKeyData,
        ]);
    }

    /**
     * Generate a new API key for the user.
     */
    public function store(ApiKeyStoreRequest $request): Response
    {
        $validated = $request->validated();

        // Delete existing API key if present
        $request->user()->apiKey?->delete();

        $key = ApiKey::generate();

        $apiKey = $request->user()->apiKey()->create([
            'key' => $key,
            'name' => $validated['name'] ?? 'API Key',
        ]);

        $apiKeyData = [
            'id' => $apiKey->id,
            'name' => $apiKey->name,
            'key' => substr($apiKey->key, 0, 10).'...'.substr($apiKey->key, -4),
            'last_used_at' => $apiKey->last_used_at?->diffForHumans(),
            'created_at' => $apiKey->created_at->diffForHumans(),
        ];

        return Inertia::render('settings/api-key', [
            'apiKey' => $apiKeyData,
            'newApiKey' => $key,
        ]);
    }

    /**
     * Delete the user's API key.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->user()->apiKey?->delete();

        return back();
    }
}
