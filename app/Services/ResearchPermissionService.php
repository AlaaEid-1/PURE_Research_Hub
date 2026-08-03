<?php

namespace App\Services;

use App\Enums\AccessRequestStatus;
use App\Enums\DownloadPermission;
use App\Models\Research;
use App\Models\ResearchAccessRequest;
use App\Models\User;

class ResearchPermissionService
{
    /**
     * Determine if a user is granted permission to download the PDF document.
     */
    public function canDownload(?User $user, Research $research): bool
    {
        if ($user !== null) {
            // Owner or Admin always granted full access
            if ($user->id === $research->user_id || $user->isAdmin()) {
                return true;
            }
        }

        // Open Access FREE papers are accessible to anyone
        if ($research->download_permission === DownloadPermission::FREE) {
            return true;
        }

        // Request Access requires an approved access request
        if ($research->download_permission === DownloadPermission::REQUEST_ACCESS) {
            if ($user === null) {
                return false;
            }

            return ResearchAccessRequest::where('research_id', $research->id)
                ->where('requester_id', $user->id)
                ->where('status', AccessRequestStatus::APPROVED->value)
                ->exists();
        }

        // Contact Author and Restricted permission papers block direct download
        return false;
    }
}
