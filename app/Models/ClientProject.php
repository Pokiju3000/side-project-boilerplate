<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['service_line_id', 'delivery_template_id', 'owner_id', 'reference', 'client_name', 'name', 'status', 'starts_on', 'target_delivery_on', 'contract_value'])]
class ClientProject extends Model
{
    protected function casts(): array
    {
        return [
            'starts_on' => 'date',
            'target_delivery_on' => 'date',
            'contract_value' => 'decimal:2',
        ];
    }

    public function serviceLine(): BelongsTo
    {
        return $this->belongsTo(ServiceLine::class);
    }

    public function deliveryTemplate(): BelongsTo
    {
        return $this->belongsTo(DeliveryTemplate::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function deliveryPlans(): HasMany
    {
        return $this->hasMany(DeliveryPlan::class);
    }

    public function latestDeliveryPlan(): HasOne
    {
        return $this->hasOne(DeliveryPlan::class)->latestOfMany();
    }

    public function budget(): HasOne
    {
        return $this->hasOne(ProjectBudget::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }
}
