<?php

namespace App\Traits;

use App\Models\Branch;

trait BranchScoped
{
    protected function userBranchId(): ?int
    {
        $user = auth()->user();

        // Admins have access to all branches
        if ($user->isAdmin()) {
            return null;
        }

        // Doctors and patients are scoped to their branch
        if ($user->isDoctor()) {
            return $user->doctorProfile?->branch_id;
        }

        if ($user->isPatient()) {
            return $user->patientProfile?->branch_id;
        }

        // Moderators are scoped to their branch
        return Branch::where('moderator_id', $user->id)->value('id');
    }

    protected function assertBranchAccess(int $branchId): void
    {
        $allowed = $this->userBranchId();

        if ($allowed !== null && $allowed !== $branchId) {
            abort(403, 'Access denied to this branch');
        }
    }
}
