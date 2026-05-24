<?php

namespace App\Http\Controllers;

use App\Models\GuestDiningSubmission;
use App\Models\SiteAnalyticsEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GuestDiningSubmissionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'channel' => 'required|in:whatsapp,email',
            'message_body' => 'required|string|max:20000',
            'items' => 'required|array|max:200',
            'items.*' => 'array',
            'grand_total_usd' => 'nullable|string|max:32',
            'session_id' => 'nullable|string|max:64',
        ]);

        GuestDiningSubmission::create([
            'channel' => $data['channel'],
            'items_json' => $data['items'],
            'message_body' => $data['message_body'],
            'grand_total_usd' => $data['grand_total_usd'] ?? null,
            'session_id' => $data['session_id'] ?? null,
        ]);

        SiteAnalyticsEvent::create([
            'event_key' => $data['channel'] === 'whatsapp' ? 'dining_submit_whatsapp' : 'dining_submit_email',
            'properties' => [
                'lines' => count($data['items']),
            ],
            'session_id' => $data['session_id'] ?? null,
        ]);

        return response()->json(['ok' => true]);
    }
}
