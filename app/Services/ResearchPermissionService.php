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

        // Check if user has an active granted access in research_access_grants
        if ($user !== null) {
            $hasActiveGrant = \App\Models\ResearchAccessGrant::where('research_id', $research->id)
                ->where('user_id', $user->id)
                ->where(function ($query) {
                    $query->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->exists();

            if ($hasActiveGrant) {
                return true;
            }
        }

        // Request Access requires an approved access request or access grant
        if ($research->download_permission === DownloadPermission::REQUEST_ACCESS) {
            if ($user === null) {
                return false;
            }

            return ResearchAccessRequest::where('research_id', $research->id)
                ->where('requester_id', $user->id)
                ->where('status', AccessRequestStatus::APPROVED->value)
                ->exists();
        }

        // Contact Author and Restricted permission papers block direct download unless active grant exists
        return false;
    }
}
