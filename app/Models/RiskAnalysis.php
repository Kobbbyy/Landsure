<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskAnalysis extends Model
{
    protected $fillable = [
        'parcel_id',
        'overall_status',
        'overall_score',
        'flood_status',
        'flood_score',
        'buffer_status',
        'buffer_score',
        'planning_status',
        'planning_score',
        'land_status',
        'land_score',
        'boundary_status',
        'boundary_score',
        'findings',
        'sources',
        'analyzed_at',
    ];

    protected $casts = [
        'overall_score' => 'decimal:2',
        'flood_score' => 'decimal:2',
        'buffer_score' => 'decimal:2',
        'planning_score' => 'decimal:2',
        'land_score' => 'decimal:2',
        'boundary_score' => 'decimal:2',
        'findings' => 'array',
        'sources' => 'array',
        'analyzed_at' => 'datetime',
    ];

    public function parcel(): BelongsTo
    {
        return $this->belongsTo(Parcel::class);
    }
}