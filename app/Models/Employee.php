<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_key', 'first_name', 'last_name', 'job_title', 'department',
        'email', 'phone', 'postal_address', 'website', 'photo_path',
        'linkedin_url', 'calendly_url', 'is_active', 'deactivated_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'deactivated_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Employee $employee) {
            if (empty($employee->slug)) {
                $employee->slug = static::generateUniqueSlug($employee->first_name, $employee->last_name);
            }
            if (empty($employee->qr_token)) {
                $employee->qr_token = (string) Str::uuid();
            }
        });
    }

    public static function generateUniqueSlug(string $first, string $last): string
    {
        $base = Str::slug($first . '-' . $last);
        $slug = $base;
        $i = 2;
        while (static::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }
        return $slug;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function publicUrl(): string
    {
        return url('/' . $this->slug);
    }

    public function fullName(): string
    {
        return $this->first_name . ' ' . strtoupper($this->last_name);
    }
}
