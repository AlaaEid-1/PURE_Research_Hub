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

                    try {
                        $encoded = $image->encode(new \Intervention\Image\Encoders\WebpEncoder(quality: 82));
                        $extension = 'webp';
                    } catch (\Exception $webpException) {
                        \Illuminate\Support\Facades\Log::warning('Avatar WebP encoding failed, falling back to JPEG', [
                            'filename' => $dto->avatar->getClientOriginalName(),
                            'mime' => $dto->avatar->getMimeType(),
                            'size' => $dto->avatar->getSize(),
                            'user_id' => $user->id,
                            'exception' => $webpException->getMessage()
                        ]);
                        $encoded = $image->encode(new \Intervention\Image\Encoders\JpegEncoder(82));
                        $extension = 'jpg';
                    }

                    $filename = Str::uuid().'.'.$extension;

                    $putResult = Storage::disk('avatars')->put('avatars/'.$filename, (string) $encoded);
                    if (!$putResult) {
                        throw new \Exception('Failed to store avatar on storage disk.');
                    }
                    $newAvatarPath = 'avatars/'.$filename;
                    $newAvatarUploaded = true;
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Avatar processing failed', [
                        'filename' => $dto->avatar->getClientOriginalName(),
                        'mime' => $dto->avatar->getMimeType(),
                        'size' => $dto->avatar->getSize(),
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
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
                Storage::disk('avatars')->delete($oldAvatarPath);
            }
        } catch (\Exception $e) {
            // Delete new avatar if DB failed
            if ($newAvatarUploaded && $newAvatarPath) {
                Storage::disk('avatars')->delete($newAvatarPath);
            }
            throw $e;
        }
    }
}
