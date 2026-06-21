<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['project_budget_id', 'category', 'label', 'amount'])]
class BudgetLine extends Model
{
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function projectBudget(): BelongsTo
    {
        return $this->belongsTo(ProjectBudget::class);
    }
}
