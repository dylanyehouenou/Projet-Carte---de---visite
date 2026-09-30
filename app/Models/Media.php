<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = [
        'name', 'original_name', 'stored_name', 'disk', 'path',
        'mime_type', 'size', 'width', 'height', 'category', 'uploaded_by',
    ];

    public function uploader(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function thumbUrl(): string
    {
        return route('admin.media.thumb', $this->id);
    }

    public function originalUrl(): string
    {
        return route('admin.media.show', $this->id);
    }

    public function formattedSize(): string
    {
        if ($this->size < 1024) return $this->size . ' o';
        if ($this->size < 1048576) return round($this->size / 1024, 1) . ' Ko';
        return round($this->size / 1048576, 1) . ' Mo';
    }
}
