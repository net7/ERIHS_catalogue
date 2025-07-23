<x-login_common>

    <div class="md:w-1/2 relative md:flex md:flex-col md:justify-center md:items-center md:pl-8 mt-8 md:mt-0">

        <div
            class="border-solid border-[#d1d5db] shadow-[0px_1px_3px_0px_rgba(0,_0,_0,_0.07)] relative flex flex-col gap-8 pb-8 px-8 border rounded-lg w-full md:w-1/2 md:ml-4">
            <!-- Right Div -->
            <div class="flex flex-col gap-4 items-start">
                <div class="bg-[#e30613] w-16 h-8 shrink-0"></div>
                <div
                    class="text-center text-2xl font-['Montserrat'] font-semibold leading-[30px] text-[#191918] self-stretch">
                    Password reset
                </div>
            </div>
            <x-validation-errors class="mb-4" />

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="block">
                    <x-label for="email" value="{{ __('Email') }}" />
                    <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)"
                        required autofocus autocomplete="username" />
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

                <div class="flex items-center justify-end mt-4">
                    <x-button>
                        {{ __('Reset Password') }}
                    </x-button>
                </div>
            </form>

        </div>
    </div>
</x-login_common>
