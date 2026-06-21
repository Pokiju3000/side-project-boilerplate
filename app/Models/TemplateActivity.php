<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['delivery_template_id', 'code', 'name', 'phase', 'planned_hours', 'sort_order'])]
class TemplateActivity extends Model
{
    protected function casts(): array
    {
        return [
            'planned_hours' => 'decimal:2',
        ];
    }

    public function deliveryTemplate(): BelongsTo
    {
        return $this->belongsTo(DeliveryTemplate::class);
    }
}
