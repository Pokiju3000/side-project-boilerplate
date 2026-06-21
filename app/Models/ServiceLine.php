<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code', 'name', 'market_segment', 'description'])]
class ServiceLine extends Model
{
    public function deliveryTemplates(): HasMany
    {
        return $this->hasMany(DeliveryTemplate::class);
    }

    public function clientProjects(): HasMany
    {
        return $this->hasMany(ClientProject::class);
    }

    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'service_line_owners')
            ->withPivot('role')
            ->withTimestamps();
    }
}
