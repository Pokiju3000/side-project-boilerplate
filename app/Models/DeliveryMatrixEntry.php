<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['delivery_plan_id', 'template_activity_id', 'delivery_week_id', 'hours'])]
class DeliveryMatrixEntry extends Model
{
    protected function casts(): array
    {
        return [
            'hours' => 'decimal:2',
        ];
    }

    public function deliveryPlan(): BelongsTo
    {
        return $this->belongsTo(DeliveryPlan::class);
    }

    public function templateActivity(): BelongsTo
    {
        return $this->belongsTo(TemplateActivity::class);
    }

    public function deliveryWeek(): BelongsTo
    {
        return $this->belongsTo(DeliveryWeek::class);
    }
}
