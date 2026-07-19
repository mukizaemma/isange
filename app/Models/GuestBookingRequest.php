<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuestBookingRequest extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_UNFORTUNATE = 'unfortunate';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_NO_SHOW = 'no_show';

    /** @var list<string> */
    public const REVIEWABLE_STATUSES = [
        self::STATUS_CONFIRMED,
        self::STATUS_UNFORTUNATE,
        self::STATUS_REJECTED,
        self::STATUS_NO_SHOW,
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

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_UNFORTUNATE => 'Fully booked',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_NO_SHOW => 'No show',
            default => 'Pending',
        };
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
