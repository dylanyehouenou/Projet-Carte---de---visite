<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Card extends Model
{
    protected $fillable = [
        'name', 'slug', 'organization_id', 'group_id', 'template_id',
        'config', 'status', 'published_at', 'created_by',
    ];

    protected $casts = [
        'config' => 'array',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Card $card) {
            if (empty($card->slug)) {
                $card->slug = Str::slug($card->name) . '-' . Str::random(8);
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(CardTemplate::class, 'template_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function employeeCard(): HasOne
    {
        return $this->hasOne(EmployeeCard::class);
    }

    public function defaultConfig(): array
    {
        return [
            'version' => 1,
            'canvas' => [
                'width' => 390,
                'height' => 844,
                'background' => ['type' => 'color', 'value' => '#003189'],
            ],
            'elements' => [],
        ];
    }
}
