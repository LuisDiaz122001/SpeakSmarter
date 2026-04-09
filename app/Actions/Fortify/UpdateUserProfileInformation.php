<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, mixed>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        $fullName = User::fullNameFromParts($input['first_name'], $input['last_name']);
        $nameParts = [
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
        ];

        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input, $nameParts, $fullName);
        } else {
            $user->forceFill([
                'name' => $fullName,
                'first_name' => $nameParts['first_name'],
                'last_name' => $nameParts['last_name'],
                'phone' => $input['phone'] ?? null,
                'email' => $input['email'],
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, mixed>  $input
     * @param  array{first_name: string, last_name: string|null}  $nameParts
     */
    protected function updateVerifiedUser(User $user, array $input, array $nameParts, string $fullName): void
    {
        $user->forceFill([
            'name' => $fullName,
            'first_name' => $nameParts['first_name'],
            'last_name' => $nameParts['last_name'],
            'phone' => $input['phone'] ?? null,
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
