<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
    public function update(User $user, Submission $submission): bool
    {
        return $user->id === $submission->user_id && $submission->status === 'Ditolak';
    }
}
