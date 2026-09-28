<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() && $user->is_active;
    }

    public function view(User $user, Employee $employee): bool
    {
        return $user->isAdmin() && $user->is_active;
    }

    public function update(User $user, Employee $employee): bool
    {
        return $user->isAdmin() && $user->is_active;
    }

    public function toggleActive(User $user, Employee $employee): bool
    {
        return $user->isAdmin() && $user->is_active;
    }
}
