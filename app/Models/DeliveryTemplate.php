<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['service_line_id', 'code', 'name', 'default_duration_weeks', 'target_margin_percent'])]
class DeliveryTemplate extends Model
{
    protected function casts(): array
    {
        return [
            'target_margin_percent' => 'decimal:2',
        ];
    }

    public function serviceLine(): BelongsTo
    {
        return $this->belongsTo(ServiceLine::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(TemplateActivity::class)->orderBy('sort_order');
    }
}
