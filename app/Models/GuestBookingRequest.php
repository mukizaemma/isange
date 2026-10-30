<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\URL;

class GuestBookingRequest extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_UNFORTUNATE = 'unfortunate';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_NO_SHOW = 'no_show';

    public const STATUS_CANCELED_BY_GUEST = 'canceled_by_guest';

    public const STATUS_CANCELED_BY_HOTEL = 'canceled_by_hotel';

    /** @var list<string> */
    public const REVIEWABLE_STATUSES = [
        self::STATUS_CONFIRMED,
        self::STATUS_UNFORTUNATE,
        self::STATUS_REJECTED,
        self::STATUS_NO_SHOW,
    ];

    /** @var list<string> */
    public const CANCELED_STATUSES = [
        self::STATUS_CANCELED_BY_GUEST,
        self::STATUS_CANCELED_BY_HOTEL,
    ];

    protected $fillable = [
        'public_id',
        'user_id',
        'room_id',
        'cart_items',
        'check_in',
        'check_out',
        'airport_pickup',
        'airport_dropoff',
        'additional_requests',
        'guest_name',
        'guest_phone',
        'guest_email',
        'guest_country',
        'payment_method',
        'total_usd',
        'discount_applied',
        'adults',
        'children',
        'fulfillment_choice',
        'completed_channel',
        'status',
        'confirmed_at',
        'reviewed_at',
        'canceled_at',
        'message_body',
        'admin_message',
    ];

    protected $casts = [
        'cart_items' => 'array',
        'check_in' => 'date',
        'check_out' => 'date',
        'airport_pickup' => 'boolean',
        'airport_dropoff' => 'boolean',
        'total_usd' => 'decimal:2',
        'discount_applied' => 'boolean',
        'confirmed_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isConfirmed(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function canBeReviewed(): bool
    {
        return $this->isPending();
    }

    public function canBeMarkedNoShow(): bool
    {
        return $this->isConfirmed();
    }

    public function isCanceled(): bool
    {
        return in_array($this->status, self::CANCELED_STATUSES, true);
    }

    public function isOpen(): bool
    {
        return $this->isPending() || $this->isConfirmed();
    }

    public function checkInHasStarted(): bool
    {
        if (! $this->check_in) {
            return false;
        }

        return now()->startOfDay()->gte($this->check_in->copy()->startOfDay());
    }

    public function canGuestUpdate(): bool
    {
        return $this->isOpen() && ! $this->checkInHasStarted();
    }

    public function canGuestCancel(): bool
    {
        return $this->canGuestUpdate();
    }

    public function canAdminCancel(?User $actor): bool
    {
        if (! $this->isOpen()) {
            return false;
        }

        if (! $this->checkInHasStarted()) {
            return true;
        }

        return $actor?->isSuperAdmin() === true;
    }

    /**
     * @return array<string, string>
     */
    public function adminStatusChoices(?User $actor = null, bool $verbs = false): array
    {
        $choices = [];

        if ($this->canBeReviewed()) {
            $choices[self::STATUS_CONFIRMED] = $verbs ? 'Confirm' : 'Confirmed';
            $choices[self::STATUS_UNFORTUNATE] = 'Fully booked';
            $choices[self::STATUS_REJECTED] = $verbs ? 'Reject (unclear)' : 'Rejected';
            $choices[self::STATUS_NO_SHOW] = 'No show';
        } elseif ($this->canBeMarkedNoShow()) {
            $choices[self::STATUS_NO_SHOW] = 'No show';
        }

        if ($this->canAdminCancel($actor)) {
            $choices[self::STATUS_CANCELED_BY_HOTEL] = $verbs ? 'Cancel (hotel)' : 'Canceled by hotel';
        }

        return $choices;
    }

    public function adminStatusError(string $newStatus, ?User $actor): ?string
    {
        if ($newStatus === self::STATUS_CANCELED_BY_HOTEL) {
            if ($this->canAdminCancel($actor)) {
                return null;
            }

            if ($this->isOpen() && $this->checkInHasStarted()) {
                return 'This reservation can no longer be canceled. After the check-in date, only the super admin can cancel it.';
            }

            return 'This reservation cannot be canceled.';
        }

        if ($this->isPending() && in_array($newStatus, self::REVIEWABLE_STATUSES, true)) {
            return null;
        }

        if ($this->isConfirmed() && $newStatus === self::STATUS_NO_SHOW) {
            return null;
        }

        return 'This booking cannot be changed to that status.';
    }

    public function guestManageUrl(): string
    {
        return URL::temporarySignedRoute(
            'reservation.manage',
            $this->guestActionLinkExpiry(),
            ['publicId' => $this->public_id]
        );
    }

    public function guestUpdateUrl(): string
    {
        return URL::temporarySignedRoute(
            'reservation.update',
            $this->guestActionLinkExpiry(),
            ['publicId' => $this->public_id]
        );
    }

    public function guestCancelUrl(): string
    {
        return URL::temporarySignedRoute(
            'reservation.cancel',
            $this->guestActionLinkExpiry(),
            ['publicId' => $this->public_id]
        );
    }

    public function guestActionLinkExpiry(): \DateTimeInterface
    {
        $checkout = $this->check_out?->copy()->endOfDay();
        $minimum = now()->addDays(45);

        if ($checkout && $checkout->greaterThan($minimum)) {
            return $checkout;
        }

        return $minimum;
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_UNFORTUNATE => 'Fully booked',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_NO_SHOW => 'No show',
            self::STATUS_CANCELED_BY_GUEST => 'Canceled by guest',
            self::STATUS_CANCELED_BY_HOTEL => 'Canceled by hotel',
            default => 'Pending',
        };
    }

    public static function paymentLabel(?string $method): string
    {
        return match ($method) {
            'pay_at_hotel', null, '' => 'Pay at hotel',
            'pay_directly', 'pay_direct', 'direct' => 'Pay directly',
            'card', 'pay_by_card' => 'Via card',
            default => ucfirst(str_replace('_', ' ', (string) $method)),
        };
    }

    public function nightsCount(): int
    {
        if (! $this->check_in || ! $this->check_out) {
            return 0;
        }

        return max(1, (int) $this->check_in->diffInDays($this->check_out));
    }

    public function stayGuestsLabel(): string
    {
        $adults = (int) ($this->adults ?? 0);
        $children = (int) ($this->children ?? 0);
        $parts = [];
        if ($adults > 0) {
            $parts[] = $adults.' adult'.($adults === 1 ? '' : 's');
        }
        if ($children > 0) {
            $parts[] = $children.' child'.($children === 1 ? '' : 'ren');
        }

        return $parts !== [] ? implode(', ', $parts) : '—';
    }

    public function roomSummary(): string
    {
        $cartRooms = is_array($this->cart_items) ? ($this->cart_items['rooms'] ?? []) : [];
        if (is_array($cartRooms) && count($cartRooms) > 1) {
            return count($cartRooms).' rooms';
        }
        if ($this->room?->roomName) {
            return $this->room->roomName;
        }
        $first = is_array($cartRooms) ? ($cartRooms[0]['name'] ?? null) : null;

        return $first ? (string) $first : '—';
    }

    public function formattedTotalUsd(): string
    {
        if ($this->total_usd === null || (float) $this->total_usd <= 0) {
            return '—';
        }

        return '$'.number_format((float) $this->total_usd, 2);
    }

    public static function channelLabel(?string $channel): string
    {
        return match ($channel) {
            'whatsapp' => 'WhatsApp',
            'email' => 'Email',
            'pay_on_delivery' => 'Pay on delivery',
            default => $channel ? ucfirst(str_replace('_', ' ', $channel)) : '—',
        };
    }

    public static function statusBadgeClass(string $status): string
    {
        return match ($status) {
            self::STATUS_CONFIRMED => 'bg-success',
            self::STATUS_UNFORTUNATE => 'bg-warning text-dark',
            self::STATUS_REJECTED => 'bg-danger',
            self::STATUS_NO_SHOW => 'bg-dark',
            self::STATUS_CANCELED_BY_GUEST => 'bg-warning text-dark',
            self::STATUS_CANCELED_BY_HOTEL => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    protected static function booted(): void
    {
        static::creating(function (GuestBookingRequest $model): void {
            if (empty($model->public_id)) {
                $model->public_id = self::generatePublicId();
            }
        });
    }

    public static function generatePublicId(): string
    {
        $start = self::referenceStart();

        $max = (int) static::query()
            ->whereRaw('public_id REGEXP \'^[0-9]{1,4}$\'')
            ->selectRaw('MAX(CAST(public_id AS UNSIGNED)) as max_ref')
            ->value('max_ref');

        $next = max($start, $max + 1);

        for ($attempt = 0; $attempt < 10000; $attempt++) {
            if ($next > 9999) {
                $next = $start;
            }

            $ref = (string) $next;
            if (! static::where('public_id', $ref)->exists()) {
                return $ref;
            }

            $next++;
        }

        throw new \RuntimeException('Unable to generate a unique booking reference.');
    }

    public static function referenceStart(): int
    {
        $start = (int) config('services.booking_reference.start', 3258);

        return max(1, min(9999, $start));
    }

    public static function appendReferenceToMessage(string $body, string $reference): string
    {
        if (preg_match('/\bReference:\s*\d{1,4}\b/i', $body)) {
            return $body;
        }

        return rtrim($body)."\n\nReference: ".$reference;
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
