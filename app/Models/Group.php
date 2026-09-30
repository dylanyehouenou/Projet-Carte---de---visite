<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Group extends Model
{
    protected $fillable = [
        'organization_id', 'name', 'slug', 'description',
        'logo_media_id', 'primary_color', 'secondary_color',
        'default_card_template_id', 'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (Group $g) {
            if (empty($g->slug)) {
                $g->slug = Str::slug($g->name) . '-' . Str::random(6);
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function defaultTemplate(): BelongsTo
    {
        return $this->belongsTo(CardTemplate::class, 'default_card_template_id');
    }

    public function logo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'logo_media_id');
    }
}
