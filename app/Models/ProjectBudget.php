<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['client_project_id', 'status', 'revenue_amount', 'cost_amount', 'margin_amount', 'margin_percent'])]
class ProjectBudget extends Model
{
    protected function casts(): array
    {
        return [
            'revenue_amount' => 'decimal:2',
            'cost_amount' => 'decimal:2',
            'margin_amount' => 'decimal:2',
            'margin_percent' => 'decimal:2',
        ];
    }

    public function clientProject(): BelongsTo
    {
        return $this->belongsTo(ClientProject::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(BudgetLine::class);
    }
}
