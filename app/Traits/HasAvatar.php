<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait HasAvatar
{
    /**
     * Get the URL to the user's avatar.
     */
    public function getAvatarUrlAttribute(): string
    {
        if (!empty($this->avatar_path) && $this->avatar_path !== '0' && $this->avatar_path !== '1') {
            return Storage::disk('avatars')->url($this->avatar_path);
        }

        return asset('images/avatar-fallback.svg');
    }

    /**
     * Delete the user's avatar if stored.
     */
    public function deleteAvatar(): void
    {
        if ($this->avatar_path) {
            try {
                if (Storage::disk('avatars')->exists($this->avatar_path)) {
                    Storage::disk('avatars')->delete($this->avatar_path);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Storage connectivity error during avatar deletion: ' . $e->getMessage());
            }
        }
    }
}
