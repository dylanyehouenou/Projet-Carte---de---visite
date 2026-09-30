<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeCard extends Model
{
    public $timestamps = false;

    protected $table = 'employee_card';

    protected $fillable = [
        'employee_id', 'card_id', 'config_overrides', 'assigned_at', 'assigned_by',
    ];

    protected $casts = [
        'config_overrides' => 'array',
        'assigned_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }
}
