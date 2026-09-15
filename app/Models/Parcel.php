<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Parcel extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'latitude',
        'longitude',
        'area_square_meters',
        'area_acres',
        'boundary',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'area_square_meters' => 'decimal:2',
        'area_acres' => 'decimal:4',
        /*
         * NOTE: 'boundary' is intentionally NOT cast here.
         *
         * The GeoJSON boundary needs both a mutator and an accessor
         * to preserve valid GeoJSON "properties" objects, and an
         * "array" cast would fight the mutator and silently encode
         * an empty "properties" as [].
         *
         * Both directions are handled explicitly below.
         */
    ];

    /**
     * Every parcel belongs to exactly one user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Ensure the GeoJSON boundary is stored with valid GeoJSON
     * "properties" objects rather than empty arrays.
     *
     * PHP's json_decode(..., true) turns both {} and [] into an
     * empty array. If we let the "array" cast re-encode the value,
     * that empty array would be stored as [] instead of {}.
     *
     * RFC 7946 requires "properties" to be an object or null.
     *
     * This mutator normalizes the boundary and json_encodes it
     * itself, converting an empty "properties" value to stdClass
     * so the stored JSON has "properties":{}.
     */
    public function setBoundaryAttribute($value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['boundary'] = null;

            return;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $value = $decoded;
            } else {
                $this->attributes['boundary'] = null;

                return;
            }
        }

        if (!is_array($value)) {
            $this->attributes['boundary'] = null;

            return;
        }

        $normalized = $this->normalizeGeoJson($value);

        $this->attributes['boundary'] = json_encode(
            $normalized,
            JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * Decode the stored GeoJSON boundary back into an array for
     * all consumers (controllers, services, views).
     *
     * This replaces the behaviour of the previous "array" cast.
     */
    protected function boundary(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($value === null || $value === '') {
                    return null;
                }

                $decoded = json_decode($value, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    return null;
                }

                return $decoded;
            },
        );
    }

    /**
     * Recursively walk the GeoJSON and replace an empty
     * "properties" array with an empty stdClass so json_encode
     * produces {} instead of [].
     */
    protected function normalizeGeoJson(array $data): array
    {
        $normalized = [];

        foreach ($data as $key => $value) {

            if (is_array($value)) {

                if ($value === [] && $key === 'properties') {
                    $normalized[$key] = new \stdClass();
                    continue;
                }

                $normalized[$key] = $this->normalizeGeoJson($value);
                continue;
            }

            $normalized[$key] = $value;
        }

        return $normalized;
    }

    public function riskAnalysis(): HasOne
    {
        return $this->hasOne(RiskAnalysis::class);
    }
}