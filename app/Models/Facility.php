<?php

namespace App\Models;

use App\Support\FrontendPageCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $table = 'facilities';

    protected $fillable = ['title', 'category', 'description', 'image', 'slug'];

    protected static function booted(): void
    {
        static::saved(static function () {
            FrontendPageCache::forgetFooterFacilities();
        });

        static::deleted(static function () {
            FrontendPageCache::forgetFooterFacilities();
        });
    }

    public function facilityImages()
    {
        return $this->hasMany(FacilityImage::class, 'facility_id');
    }
}
