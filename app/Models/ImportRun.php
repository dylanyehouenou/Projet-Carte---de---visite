<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportRun extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'filename', 'imported_by', 'rows_total', 'rows_created',
        'rows_updated', 'rows_unchanged', 'rows_missing', 'rows_failed', 'report_json',
    ];

    protected function casts(): array
    {
        return [
            'report_json' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function importedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'imported_by');
    }
}
