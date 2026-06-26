<?php

namespace App\Http\Controllers;

use App\Models\GuestDiningSubmission;
use App\Models\SiteAnalyticsEvent;
use App\Support\SpamProtection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GuestDiningSubmissionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            SpamProtection::validateRequest($request);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => collect($e->errors())->flatten()->first() ?? SpamProtection::failureMessage(),
            ], 422);
        }

        $data = $request->validate([
            'channel' => 'required|in:whatsapp,email',
            'message_body' => 'required|string|max:20000',
            'items' => 'required|array|max:200',
            'items.*' => 'array',
            'guest_name' => 'nullable|string|max:255',
            'guest_phone' => 'nullable|string|max:64',
            'guest_email' => 'nullable|email|max:255',
            'special_requests' => 'nullable|string|max:5000',
            'currency' => 'nullable|in:usd,rwf',
            'grand_total_usd' => 'nullable|string|max:32',
            'grand_total_rwf' => 'nullable|string|max:32',
            'session_id' => 'nullable|string|max:64',
        ]);

        if ($data['channel'] === 'whatsapp' && strlen(preg_replace('/\D+/', '', (string) ($data['guest_phone'] ?? ''))) < 8) {
            return response()->json(['message' => 'A valid WhatsApp number is required.'], 422);
        }
        if ($data['channel'] === 'email' && empty($data['guest_email'])) {
            return response()->json(['message' => 'An email address is required.'], 422);
        }

        GuestDiningSubmission::create([
            'channel' => $data['channel'],
            'guest_name' => $data['guest_name'] ?? null,
            'guest_phone' => $data['guest_phone'] ?? null,
            'guest_email' => $data['guest_email'] ?? null,
            'special_requests' => $data['special_requests'] ?? null,
            'currency' => $data['currency'] ?? 'usd',
            'items_json' => $data['items'],
            'message_body' => $data['message_body'],
            'grand_total_usd' => $data['grand_total_usd'] ?? null,
            'grand_total_rwf' => $data['grand_total_rwf'] ?? null,
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
