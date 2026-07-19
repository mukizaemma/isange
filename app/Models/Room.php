<?php

namespace App\Models;

use App\Support\Currency;
use App\Support\FrontendPageCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Room extends Model
{
    use HasFactory;

    protected $table = 'rooms';

    public const TYPE_ROOM = 'room';

    public const TYPE_APARTMENT = 'apartment';

    public const DISCOUNT_PERCENT = 'percent';

    public const DISCOUNT_FIXED = 'fixed';

    protected $fillable = [
        'roomName',
        'category',
        'accommodation_type',
        'image',
        'slug',
        'price',
        'price_rwf',
        'discount_enabled',
        'discount_type',
        'discount_value',
        'size',
        'quantity',
        'maxAdults',
        'maxChildren',
        'description',
    ];

    protected static function booted(): void
    {
        static::saved(static function () {
            FrontendPageCache::forgetHomePage();
        });

        static::deleted(static function () {
            FrontendPageCache::forgetHomePage();
        });
    }

    protected $casts = [
        'price' => 'decimal:2',
        'price_rwf' => 'decimal:2',
        'discount_enabled' => 'boolean',
        'discount_value' => 'decimal:2',
    ];

    public function reservation()
    {
        return $this->hasMany(Reservation::class);
    }

    public function images()
    {
        return $this->hasMany(roomImage::class);
    }

    public function amenityOptions(): BelongsToMany
    {
        return $this->belongsToMany(HotelAmenityOption::class, 'hotel_amenity_room', 'room_id', 'hotel_amenity_option_id')->withTimestamps();
    }

    /** @deprecated Use price — kept for older templates. */
    public function getSinglePriceAttribute(): ?float
    {
        return $this->price !== null ? (float) $this->price : null;
    }

    public function listPriceUsd(): ?float
    {
        if ($this->price === null || (float) $this->price <= 0) {
            return null;
        }

        return round((float) $this->price, 2);
    }

    public function hasActiveDiscount(): bool
    {
        $list = $this->listPriceUsd();
        if ($list === null || ! $this->discount_enabled) {
            return false;
        }

        $value = (float) ($this->discount_value ?? 0);
        if ($value <= 0) {
            return false;
        }

        if ($this->discount_type === self::DISCOUNT_PERCENT) {
            return $value > 0 && $value <= 100;
        }

        if ($this->discount_type === self::DISCOUNT_FIXED) {
            return $value > 0 && $value < $list;
        }

        return false;
    }

    public function salePriceUsd(): ?float
    {
        $list = $this->listPriceUsd();
        if ($list === null) {
            return null;
        }

        if (! $this->hasActiveDiscount()) {
            return $list;
        }

        if ($this->discount_type === self::DISCOUNT_PERCENT) {
            $sale = $list * (1 - ((float) $this->discount_value / 100));
        } else {
            $sale = $list - (float) $this->discount_value;
        }

        return round(max(0, $sale), 2);
    }

    public function bookingPriceUsd(bool $discountEligible): ?float
    {
        return $discountEligible ? $this->salePriceUsd() : $this->listPriceUsd();
    }

    public function salePriceRwf(): ?float
    {
        $sale = $this->salePriceUsd();
        if ($sale === null) {
            return null;
        }

        $list = $this->listPriceUsd();
        $hasStoredRwf = $this->price_rwf !== null && (float) $this->price_rwf > 0;

        if ($hasStoredRwf && $list !== null && $list > 0) {
            if ($this->hasActiveDiscount()) {
                return round(((float) $this->price_rwf) * ($sale / $list), 0);
            }

            return round((float) $this->price_rwf, 0);
        }

        return Currency::usdToRwf($sale);
    }

    public function bookingPriceRwf(bool $discountEligible): ?float
    {
        if ($discountEligible) {
            return $this->salePriceRwf();
        }

        if ($this->price_rwf !== null && (float) $this->price_rwf > 0) {
            return round((float) $this->price_rwf, 0);
        }

        $list = $this->listPriceUsd();

        return $list === null ? null : Currency::usdToRwf($list);
    }

    public function savingsUsd(): ?float
    {
        if (! $this->hasActiveDiscount()) {
            return null;
        }

        $list = $this->listPriceUsd();
        $sale = $this->salePriceUsd();
        if ($list === null || $sale === null) {
            return null;
        }

        return round(max(0, $list - $sale), 2);
    }

    public function effectiveDiscountPercent(): ?float
    {
        $list = $this->listPriceUsd();
        $saving = $this->savingsUsd();
        if ($list === null || $list <= 0 || $saving === null || $saving <= 0) {
            return null;
        }

        return round(($saving / $list) * 100, 1);
    }

    public function discountBadgeLabel(): ?string
    {
        if (! $this->hasActiveDiscount()) {
            return null;
        }

        if ($this->discount_type === self::DISCOUNT_PERCENT) {
            $pct = (float) $this->discount_value;
            $fmt = $pct == floor($pct) ? (string) (int) $pct : number_format($pct, 1);

            return $fmt.'% off';
        }

        $save = $this->savingsUsd();
        if ($save === null) {
            return null;
        }

        $fmt = $save == floor($save) ? number_format($save, 0) : number_format($save, 2);

        return '$'.$fmt.' off';
    }

    public function discountTooltip(): string
    {
        if (! $this->hasActiveDiscount()) {
            return '';
        }

        $list = $this->listPriceUsd();
        $sale = $this->salePriceUsd();
        $save = $this->savingsUsd();
        $badge = $this->discountBadgeLabel();

        $listFmt = '$'.number_format((float) $list, $list == floor($list) ? 0 : 2);
        $saleFmt = '$'.number_format((float) $sale, $sale == floor($sale) ? 0 : 2);
        $saveFmt = '$'.number_format((float) $save, $save == floor($save) ? 0 : 2);

        return "Promotional rate: was {$listFmt}, now {$saleFmt} ({$badge} — save {$saveFmt} per night).";
    }
}
