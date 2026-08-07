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
        $exists = false;
        $absPath = null;
        $size = 0;
        if ($this->avatar_path) {
            $exists = Storage::disk('public')->exists($this->avatar_path);
            if ($exists) {
                $absPath = Storage::disk('public')->path($this->avatar_path);
                $size = filesize($absPath);
            }
        }
        
        \Illuminate\Support\Facades\Log::info('IMAGE_DEBUG', [
            'requested_path' => $this->avatar_path,
            'disk' => 'public',
            'absolute_path' => $absPath,
            'exists' => $exists,
            'filesize' => $size,
        ]);

        if ($this->avatar_path && $exists) {
            return Storage::disk('public')->url($this->avatar_path);
        }

        return asset('images/avatar-fallback.svg');
    }

    /**
     * Delete the user's avatar if stored.
     */
    public function deleteAvatar(): void
    {
        if ($this->avatar_path && Storage::disk('public')->exists($this->avatar_path)) {
            Storage::disk('public')->delete($this->avatar_path);
        }
    }
}
