<?php

namespace App\Http\Controllers;

use App\Models\SiteAnalyticsEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiteAnalyticsController extends Controller
{
    /**
     * Lightweight aggregate tracking (no PII in properties).
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'event_key' => 'required|string|max:80|regex:/^[a-z0-9_]+$/',
            'properties' => 'nullable|array|max:30',
            'session_id' => 'nullable|string|max:64',
        ]);

        SiteAnalyticsEvent::create([
            'event_key' => $data['event_key'],
            'properties' => $data['properties'] ?? [],
            'session_id' => $data['session_id'] ?? null,
        ]);

        return response()->json(['ok' => true]);
    }
}
