<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AuditService
{
    public function log(User $user, string $action, ?Model $target = null, array $metadata = []): void
    {
        AuditLog::create([
            'user_id'     => $user->id,
            'action'      => $action,
            'target_type' => $target ? get_class($target) : null,
            'target_id'   => $target?->getKey(),
            'metadata'    => $metadata ?: null,
        ]);
    }
}
