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
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'nationality' => ['required', 'string', 'max:50'],
            'birth_year' => ['required', 'string', 'max:4'],
            'gender' => ['required'],
            'home_institution' => ['required', 'string', 'max:255'],
            'institution_address' => ['required', 'string', 'max:255'],
            'institution_city' => ['required', 'string', 'max:255'],
            'institution_status_code' => ['required'],
            'institution_country' => ['required'],
            'job' => ['required', 'string', 'max:255'],
            'academic_background' => ['required', 'string', 'max:255'],
            'position' => ['required'],
            'office_phone' => ['required', 'string', 'max:255'],
            'mobile_phone' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        if (
            $input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail
        ) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'surname' => $input['surname'],
                'nationality' => $input['nationality'],
                'birth_year' => $input['birth_year'],
                'gender' => $input['gender'],
                'home_institution' => $input['home_institution'],
                'institution_address' => $input['institution_address'],
                'institution_city' => $input['institution_city'],
                'institution_status_code' => $input['institution_status_code'],
                'institution_country' => $input['institution_country'],
                'job' => $input['job'],
                'academic_background' => $input['academic_background'],
                'position' => $input['position'],
                'office_phone' => $input['office_phone'],
                'mobile_phone' => $input['mobile_phone'],
                'email' => $input['email'],
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
