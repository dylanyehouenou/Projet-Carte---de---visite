<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CardTemplate extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'category',
        'thumbnail_media_id', 'config', 'is_system', 'created_by',
    ];

    protected $casts = [
        'config' => 'array',
        'is_system' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (CardTemplate $t) {
            if (empty($t->slug)) {
                $t->slug = Str::slug($t->name) . '-' . Str::random(6);
            }
        });
    }

    public function thumbnail(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Media::class, 'thumbnail_media_id');
    }
}
