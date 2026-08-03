<?php

namespace App\Policies;

use App\Models\Research;
use App\Models\User;
use App\Services\ResearchPermissionService;

class ResearchPolicy
{
    /**
     * Determine whether the user can view any research publications.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the research publication.
     */
    public function view(?User $user, Research $research): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create research publications.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the research publication.
     */
    public function update(User $user, Research $research): bool
    {
        return $user->id === $research->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the research publication.
     */
    public function delete(User $user, Research $research): bool
    {
        return $user->id === $research->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can download the PDF file of the research.
     */
    public function download(?User $user, Research $research): bool
    {
        return app(ResearchPermissionService::class)->canDownload($user, $research);
    }
}
