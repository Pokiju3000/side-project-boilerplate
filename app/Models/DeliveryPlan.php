<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['client_project_id', 'name', 'status', 'starts_on', 'ends_on', 'planned_hours', 'allocated_hours', 'validated_at'])]
class DeliveryPlan extends Model
{
    protected function casts(): array
    {
        return [
            'starts_on' => 'date',
            'ends_on' => 'date',
            'planned_hours' => 'decimal:2',
            'allocated_hours' => 'decimal:2',
            'validated_at' => 'datetime',
        ];
    }

    public function clientProject(): BelongsTo
    {
        return $this->belongsTo(ClientProject::class);
    }

    public function weeks(): HasMany
    {
        return $this->hasMany(DeliveryWeek::class)->orderBy('week_number');
    }

    public function matrixEntries(): HasMany
    {
        return $this->hasMany(DeliveryMatrixEntry::class);
    }
}
