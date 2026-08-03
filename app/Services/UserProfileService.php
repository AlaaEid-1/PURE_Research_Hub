<?php

namespace App\Services;

use App\DTOs\UserProfileDTO;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class UserProfileService
{
    /**
     * Update user profile information.
     */
    public function updateProfile(User $user, UserProfileDTO $dto): void
    {
        $avatarPath = $user->avatar_path;

        if ($dto->avatar) {
            $user->deleteAvatar();

            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->decodePath($dto->avatar->getRealPath());
            $image->scaleDown(width: 400, height: 400);

            $encoded = $image->encode(new \Intervention\Image\Encoders\WebpEncoder(quality: 82));
            $filename = Str::uuid().'.webp';

            Storage::disk('public')->put('avatars/'.$filename, (string) $encoded);
            $avatarPath = 'avatars/'.$filename;
        }

        $emailChanged = $dto->email !== $user->email;

        $user->forceFill([
            'name' => $dto->name,
            'email' => $dto->email,
            'institution' => $dto->institution,
            'department' => $dto->department,
            'bio' => $dto->bio,
            'research_interests' => $dto->researchInterests,
            'orcid_id' => $dto->orcidId,
            'google_scholar_url' => $dto->googleScholarUrl,
            'website_url' => $dto->websiteUrl,
            'avatar_path' => $avatarPath,
            'email_verified_at' => $emailChanged && $user instanceof MustVerifyEmail ? null : $user->email_verified_at,
        ])->save();

        if ($emailChanged && $user instanceof MustVerifyEmail) {
            $user->sendEmailVerificationNotification();
        }
    }
}
