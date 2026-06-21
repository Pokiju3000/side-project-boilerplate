<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['source', 'entity', 'status', 'records_count', 'warnings_count', 'started_at', 'finished_at', 'last_synced_at', 'notes'])]
class ExternalImportRun extends Model
{
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'last_synced_at' => 'datetime',
        ];
    }
}
