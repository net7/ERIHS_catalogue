<x-login_common>
    <div class="md:w-1/2 relative md:flex md:flex-col md:justify-center md:items-center md:pl-8 mt-8 md:mt-0">

        <div class="border-solid border-[#d1d5db] shadow-[0px_1px_3px_0px_rgba(0,_0,_0,_0.07)] relative flex flex-col gap-8 pb-8 px-8 border rounded-lg w-full md:w-1/2 md:ml-4">

            <!-- Right Div -->
            <div class="flex flex-col gap-4 items-start">
                <div class="bg-[#e30613] w-16 h-8 shrink-0"></div>
                <div
                    class="text-center text-2xl font-['Montserrat'] font-semibold leading-[30px] text-[#191918] self-stretch">
                    Register
                </div>
            </div>
            <x-validation-errors class="mb-4" />

            <form class="flex flex-col gap-3" method="POST" action="{{ route('register') }}">
                @csrf
                <div class="flex flex-col gap-3">
                    <div>
                        <x-label for="name" value="{{ __('Name') }}" />
                        <x-input id="name" class="block mt-1 w-full" type="text" name="name"
                            :value="old('name')" required autofocus autocomplete="name" />
                    </div>

                    <div>
                        <x-label for="surname" value="{{ __('Surname') }}" />
                        <x-input id="surname" class="block mt-1 w-full" type="text" name="surname"
                            :value="old('name')" required autofocus autocomplete="surname" />
                    </div>

                    <div class="mt-4">
                        <x-label for="email" value="{{ __('Email') }}" />
                        <x-input id="email" class="block mt-1 w-full" type="email" name="email"
                            :value="old('email')" required autocomplete="username" />
                    </div>

                    <div class="mt-4">
                        <x-label for="password" value="{{ __('Password') }}" />
                        <x-input id="password" class="block mt-1 w-full" type="password" name="password" required
                            autocomplete="new-password" />
                    </div>

                    <div class="mt-4">
                        <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                        <x-input id="password_confirmation" class="block mt-1 w-full" type="password"
                            name="password_confirmation" required autocomplete="new-password" />
                    </div>

                    @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                        <div class="mt-4">
                            <x-label for="terms">
                                <div class="flex items-center">
                                    <x-checkbox name="terms" id="terms" required />

                                    <div class="ml-2">
                                        <!-- {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                            'terms_of_service' =>
                                                '<a target="_blank" href="' .
                                                route('terms.show') .
                                                '" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">' .
                                                __('Terms of Service') .
                                                '</a>',
                                            'privacy_policy' =>
                                                '<a target="_blank" href="' .
                                                route('policy.show') .
                                                '" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">' .
                                                __('Privacy Policy') .
                                                '</a>',
                                        ]) !!} -->


                                        {!! __('I agree to the :terms_of_service and :privacy_policy', [
                        'terms_of_service' =>
                        '<a target="_blank" href="https://zenodo.org/records/8406447" class="font-bold underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">' .
                            __('Access Policy') .
                            '</a>',
                        'privacy_policy' =>
                        '<a target="_blank" href="https://www.e-rihs.eu/privacy-policy/" class="font-bold underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">' .
                            __('Privacy Policy') .
                            '</a>',
                        ]) !!}
                                    </div>
                                </div>
                            </x-label>
                        </div>
                    @endif

                    <div class="flex items-center justify-end mt-4">
                        <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            href="{{ route('login') }}">
                            {{ __('Already registered?') }}
                        </a>

                        <x-button class="ml-4">
                            {{ __('Register') }}
                        </x-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    {{--
        @if (JoelButcher\Socialstream\Socialstream::show())
            <x-socialstream />
            <div class="flex items-center justify-center">
                <a class="ml-2" href="/login/orcid">
                    <x-socialstream-icons.orcid class="h-6 w-6 mx-2" />
                    <span class="sr-only">ORCID</span>
                </a>
            </div>
        @endif --}}
</x-login_common>
