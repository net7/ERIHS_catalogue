<x-login_common>

    <div class="md:w-1/2 relative md:flex md:flex-col md:justify-center md:items-center md:pl-8 mt-8 md:mt-0">

        <div class="border-solid border-[#d1d5db] shadow-[0px_1px_3px_0px_rgba(0,_0,_0,_0.07)] relative flex flex-col gap-8 pb-8 px-8 border rounded-lg w-full md:w-1/2 md:ml-4">
            <!-- Right Div -->


            <div class="flex flex-col gap-4 items-start">
                <div class="bg-[#e30613] w-16 h-8 shrink-0"></div>
                <div
                    class="text-center text-2xl font-['Montserrat'] font-semibold leading-[30px] text-[#191918] self-stretch">
                    Sign In
                </div>
            </div>
            <div class="flex flex-col gap-4">
                <a href="{{ route('orcid_login') }}"
                    class="border-solid border-[#d1d5db] shadow-[0px_1px_3px_0px_rgba(0,_0,_0,_0.07)] bg-[#f9fafb] flex flex-row justify-center gap-4 h-10 shrink-0 items-center border rounded-lg">
                    <img src="{{ url('images/orcid_logo.png') }}" class="min-h-0 min-w-0 w-4 shrink-0" />
                    <div
                        class="text-center whitespace-nowrap text-sm font-['Montserrat'] font-semibold leading-[20px] text-[#1f2937] w-[139px] shrink-0">
                        Sign In with ORCID
                    </div>
                </a>
                <!-- <a href="/login/edugain"
                    class="border-solid border-[#d1d5db] shadow-[0px_1px_3px_0px_rgba(0,_0,_0,_0.07)] bg-[#f9fafb] flex flex-row justify-center gap-4 h-10 shrink-0 items-center border rounded-lg">
                    <img src="{{ url('images/edugain_logo.svg') }}" class="min-h-0 min-w-0 w-4 shrink-0" />
                    <div
                        class="text-center whitespace-nowrap text-sm font-['Montserrat'] font-semibold leading-[20px] text-[#1f2937] w-[139px] shrink-0">
                        Sign In with EduGAIN
                    </div>
                </a>
                <a href="/login/eduteams"
                    class="border-solid border-[#d1d5db] shadow-[0px_1px_3px_0px_rgba(0,_0,_0,_0.07)] bg-[#f9fafb] flex flex-row justify-center gap-4 h-10 shrink-0 items-center border rounded-lg">
                    <img src="{{ url('images/eduteams_logo.svg') }}" class="min-h-0 min-w-0 w-4 shrink-0" />
                    <div
                        class="text-center whitespace-nowrap text-sm font-['Montserrat'] font-semibold leading-[20px] text-[#1f2937] w-[139px] shrink-0">
                        Sign In with EduTEAMS
                    </div>
                </a> -->
            </div>
            <x-validation-errors class="mb-4" />
            <div class="flex flex-row justify-between items-center">
                <div class="border-solid border-[#e5e7eb] w-2/5 h-px border-t border-b-0 border-x-0"></div>
                <div class="text-center font-['Montserrat'] leading-[24px] text-[#6b7280] w-5 shrink-0">
                    Or
                </div>
                <div class="border-solid border-[#e5e7eb] w-2/5 h-px border-t border-b-0 border-x-0"></div>
            </div>
            <form class="flex flex-col gap-3" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="flex flex-col gap-3">
                    <div class="flex flex-col gap-1 items-start">
                        <div class="flex flex-row gap-1 w-12 items-center">
                            <div class="text-sm font-['Montserrat'] font-semibold leading-[20px] text-[#374151] w-12">
                                Email
                            </div>
                            <div
                                class="text-sm font-['Montserrat'] font-semibold leading-[20px] text-[#b4173a] w-1 shrink-0">
                                *
                            </div>
                        </div>
                        <input type="email" name="email"
                            class="border-solid border-[#d1d5db] shadow-[0px_1px_3px_0px_rgba(0,_0,_0,_0.07)] self-stretch flex flex-col h-10 shrink-0 px-3 py-2 border rounded-lg"
                            required="">
                    </div>
                    <div class="flex flex-col justify-between gap-1">
                        <div class="self-start flex flex-row gap-3 w-20 items-center">
                            <div class="text-sm font-['Montserrat'] font-semibold leading-[20px] text-[#374151] w-16">
                                Password
                            </div>
                            <div
                                class="text-sm font-['Montserrat'] font-semibold leading-[20px] text-[#b4173a] w-1 shrink-0">
                                *
                            </div>
                        </div>
                        <div class="flex items-center space-x-4 relative">
                            <input type="password" id="password" name="password"
                                class="border border-gray-300 shadow-sm flex-1 h-10 px-4 py-2 rounded-lg"
                                required="">
                            <button type="button" id="togglePassword" class="absolute right-0 p-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                    </path>
                                </svg>
                            </button>
                        </div>
                        <a href="/forgot-password" class="text-sm font-['Montserrat'] leading-[20px] text-[#d97706]">
                            Have you forgotten the password?
                        </a>
                    </div>
                </div>
                {{-- <div class="self-start flex flex-row gap-3 items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <div class="text-sm font-['Montserrat'] leading-[20px] text-[#374151] w-32">
                        Remember me
                    </div>
                </div> --}}
                <div class="flex flex-col gap-4">
                    <button
                        class="shadow-[0px_1px_3px_0px_rgba(0,_0,_0,_0.16)] bg-[#2d4547] flex flex-col justify-center h-10 shrink-0 items-center rounded-lg text-[#f9fafb]">
                        Continue
                    </button>
                    <div class="text-center text-xs font-['Montserrat'] leading-[16px] text-[#374151]">
                        {!! __('By configuring you agree to E-RIHS :terms_of_service and :privacy_policy', [
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
                    <div>
                        <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                           href="{{ route('register') }}">
                            {{ __('Register') }}
                        </a>
                    </div>
            </form>

        </div>
    </div>
    <script>
        const togglePasswordButton = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePasswordButton.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            togglePasswordButton.querySelector('svg').classList.toggle('text-gray-600', type === 'password');
            togglePasswordButton.querySelector('svg').classList.toggle('text-gray-400', type !== 'password');
        });
    </script>
</x-login_common>
