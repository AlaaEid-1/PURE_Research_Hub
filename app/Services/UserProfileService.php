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
        $oldAvatarPath = $user->avatar_path;
        $newAvatarPath = $oldAvatarPath;
        $newAvatarUploaded = false;

        try {
            if ($dto->avatar) {
                try {
                    $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                    $image = $manager->decodePath($dto->avatar->getRealPath());
                    $image->scaleDown(width: 400, height: 400);

                    $encoded = $image->encode(new \Intervention\Image\Encoders\WebpEncoder(quality: 82));
                    $filename = Str::uuid().'.webp';

                    Storage::disk('public')->put('avatars/'.$filename, (string) $encoded);
                    $newAvatarPath = 'avatars/'.$filename;
                    $newAvatarUploaded = true;
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Avatar processing failed', ['error' => $e->getMessage()]);
                    throw \Illuminate\Validation\ValidationException::withMessages(['avatar' => 'The uploaded image could not be processed.']);
                }
            }

            \Illuminate\Support\Facades\DB::transaction(function () use ($user, $dto, $newAvatarPath) {
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
                    'avatar_path' => $newAvatarPath,
                    'email_verified_at' => $emailChanged && $user instanceof MustVerifyEmail ? null : $user->email_verified_at,
                ])->save();

                if ($emailChanged && $user instanceof MustVerifyEmail) {
                    $user->sendEmailVerificationNotification();
                }
            });

            // Delete old avatar only after DB success
            if ($newAvatarUploaded && $oldAvatarPath) {
                Storage::disk('public')->delete($oldAvatarPath);
            }
        } catch (\Exception $e) {
            // Delete new avatar if DB failed
            if ($newAvatarUploaded && $newAvatarPath) {
                Storage::disk('public')->delete($newAvatarPath);
            }
            throw $e;
        }
    }
}
