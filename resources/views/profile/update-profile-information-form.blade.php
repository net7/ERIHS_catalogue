<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('Profile Information') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Update your account\'s profile information and email address.') }}
    </x-slot>

    <x-slot name="form">
        <!-- Profile Photo -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
        <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-4">
            <!-- Profile Photo File Input -->
            <input type="file" class="hidden" wire:model.live="photo" x-ref="photo" x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            " />

            <x-label for="photo" value="{{ __('Photo') }}" />

            <!-- Current Profile Photo -->
            <div class="mt-2" x-show="! photoPreview">
                <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}" class="rounded-full h-20 w-20 object-cover">
            </div>

            <!-- New Profile Photo Preview -->
            <div class="mt-2" x-show="photoPreview" style="display: none;">
                <span class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center" x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                </span>
            </div>

            <x-secondary-button class="mt-2 mr-2" type="button" x-on:click.prevent="$refs.photo.click()">
                {{ __('Select A New Photo') }}
            </x-secondary-button>

            @if ($this->user->profile_photo_path)
            <x-secondary-button type="button" class="mt-2" wire:click="deleteProfilePhoto">
                {{ __('Remove Photo') }}
            </x-secondary-button>
            @endif

            <x-input-error for="photo" class="mt-2" />
        </div>
        @endif

        <!-- Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="name" value="{{ __('Name *') }}" />
            <x-input id="name" type="text" class="mt-1 block w-full" wire:model="state.name" autocomplete="name" />
            <x-input-error for="name" class="mt-2" />
        </div>
        <!-- Surname -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="surname" value="{{ __('Surname *') }}" />
            <x-input id="surname" type="text" class="mt-1 block w-full" wire:model="state.surname" autocomplete="surname" />
            <x-input-error for="surname" class="mt-2" />
        </div>
        <!-- Nationality -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="nationality" value="{{ __('Nationality *') }}" />
            <x-input id="nationality" type="text" class="mt-1 block w-full" wire:model="state.nationality" autocomplete="nationality" />
            <x-input-error for="nationality" class="mt-2" />
        </div>
        <!-- Birth year -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="birth_year" value="{{ __('Birth Year *') }}" />
            <x-input id="birth_year" type="text" class="mt-1 block w-full" wire:model="state.birth_year" autocomplete="birth_year" />
            <x-input-error for="birth_year" class="mt-2" />
        </div>
        <!-- Gender -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="gender" value="{{ __('Gender *') }}" />
            <select id="gender" class="block mt-1 w-full" wire:model="state.gender">
                <option value=''></option>
@php
                $genders=App\Enums\Gender::options();
                foreach($genders as $label=>$value) {
                    echo "<option value='".$label."'>".$label."</option>";
                }
@endphp
            </select>
            <x-input-error for="gender" class="mt-2" />
        </div>
        <!-- Home Institution -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="home_institution" value="{{ __('Home Institution (HI) *') }}" />
            <x-input id="home_institution" type="text" class="mt-1 block w-full" wire:model="state.home_institution" autocomplete="home_institution" />
            <x-input-error for="home_institution" class="mt-2" />
        </div>
        <!-- Institution Address -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="institution_address" value="{{ __('Institution Address *') }}" />
            <x-input id="institution_address" type="text" class="mt-1 block w-full" wire:model="state.institution_address" autocomplete="institution_address" />
            <x-input-error for="institution_address" class="mt-2" />
        </div>
        <!-- City -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="institution_city" value="{{ __('City *') }}" />
            <x-input id="institution_city" type="text" class="mt-1 block w-full" wire:model="state.institution_city" autocomplete="institution_city" />
            <x-input-error for="institution_city" class="mt-2" />
        </div>
        <!-- Institution Status Code -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="institution_status_code" value="{{ __('HI Legal Status Code *') }}" />
            <select id="institution_status_code" class="block mt-1 w-full" wire:model="state.institution_status_code">
                <option value=''></option>
@php
                $instStatusCodes=App\Enums\InstStatusCode::options();
@endphp
                @foreach($instStatusCodes as $label=>$value)
                    <option value="{{$label}}">{{$label}}</option>;
                @endforeach
            </select>
            <x-input-error for="institution_status_code" class="mt-2" />
        </div>
        <!-- Institution Country Code -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="institution_country" value="{{ __('HI Country Code *') }}" />
            <select id="institution_country" class="block mt-1 w-full" wire:model="state.institution_country">
                <option value=''></option>
@php
                $countries=(new App\Services\CountryService())->getCountries();
@endphp
                @foreach ($countries as $country)
                    <option value="{{$country->code}}">{{$country->name}}</option>
                @endforeach
            </select>
            <x-input-error for="institution_country" class="mt-2" />
        </div>
        <!-- Job -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="job" value="{{ __('Job *') }}" />
            <x-input id="job" type="text" class="mt-1 block w-full" wire:model="state.job" autocomplete="job" />
            <x-input-error for="job" class="mt-2" />
        </div>
        <!-- Academic Background -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="academic_background" value="{{ __('Academic Background *') }}" />
            <x-input id="academic_background" type="text" class="mt-1 block w-full" wire:model="state.academic_background" autocomplete="academic_background" />
            <x-input-error for="academic_background" class="mt-2" />
        </div>
        <!-- Position -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="position" value="{{ __('Position *') }}" />
            <select id="position" class="block mt-1 w-full" wire:model="state.position">
                <option value=''></option>
@php
                $positions=App\Enums\Position::options();
@endphp
                @foreach($positions as $label=>$value)
                    <option value="{{$label}}">{{$label}}</option>;
                @endforeach
            </select>
            <x-input-error for="position" class="mt-2" />
        </div>
        <!-- Office Phone -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="office_phone" value="{{ __('Office Phone *') }}" />
            <x-input id="office_phone" type="text" class="mt-1 block w-full" wire:model="state.office_phone" autocomplete="office_phone" />
            <x-input-error for="office_phone" class="mt-2" />
        </div>
        <!-- Mobile Phone -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="mobile_phone" value="{{ __('Mobile Phone *') }}" />
            <x-input id="mobile_phone" type="text" class="mt-1 block w-full" wire:model="state.mobile_phone" autocomplete="mobile_phone" />
            <x-input-error for="mobile_phone" class="mt-2" />
        </div>
        <!-- Email -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="email" value="{{ __('Email *') }}" />
            <x-input id="email" type="email" class="mt-1 block w-full" wire:model="state.email" autocomplete="username" />
            <x-input-error for="email" class="mt-2" />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
            <p class="text-sm mt-2">
                {{ __('Your email address is unverified.') }}

                <button type="button" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" wire:click.prevent="sendEmailVerification">
                    {{ __('Click here to re-send the verification email.') }}
                </button>
            </p>

            @if ($this->verificationLinkSent)
            <p class="mt-2 font-medium text-sm text-green-600">
                {{ __('A new verification link has been sent to your email address.') }}
            </p>
            @endif
            @endif
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="mr-3" on="saved">
            {{ __('Saved.') }}
        </x-action-message>

        <x-button wire:loading.attr="disabled" wire:target="photo">
            {{ __('Save') }}
        </x-button>
    </x-slot>
</x-form-section>
