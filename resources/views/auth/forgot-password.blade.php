<x-login_common>
    <div class="md:w-1/2 relative md:flex md:flex-col md:justify-center md:items-center md:pl-8 mt-8 md:mt-0">
        <div class="border-solid border-[#d1d5db] shadow-[0px_1px_3px_0px_rgba(0,_0,_0,_0.07)] relative flex flex-col md:justify-between gap-10 pb-8 px-8 border rounded-lg md:w-96">


            @if (session()->has('message'))
                @php
                    $body = '';
                    if (session('message') == 'warning') {
                        $body = 'Your email is linked with a social. Cannot reset password!';
                        Notification::make()
                            ->title('Password Reset')
                            ->body($body)
                            ->warning()
                            ->send();
                    } elseif (session('message') == 'success') {
                        $body = 'We sent an email to reset your password. Please check it!';
                        Notification::make()
                            ->title('Password Reset')
                            ->body($body)
                            ->success()
                            ->send();
                    } else {
                        $body = 'The email you provided is not in our database. Please register or log in using a social.';
                        Notification::make()
                            ->title('Password Reset')
                            ->body($body)
                            ->danger()
                            ->send();
                    }
                @endphp
            @endif


            {{-- {{ dd (session())}} --}}

            @if (session()->has('message'))
                asasdfsadf
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session()->get('message') }}
                </div>
            @endif


            <!-- Right Div -->
            {{-- <div class="md:w-1/2 relative md:flex md:flex-col md:justify-center md:items-center md:pl-8 mt-8 md:mt-0">
            <div class="border-solid border-[#d1d5db] shadow-[0px_1px_3px_0px_rgba(0,_0,_0,_0.07)] relative flex flex-col md:justify-between gap-10 pb-8 px-8 border rounded-lg md:w-96"> --}}

            <form action=" {{ route('password.email') }}" method="POST">
                @csrf



                <div class="flex flex-col justify-between gap-4">
                    <div class="bg-[#e30613] self-start w-16 h-8 shrink-0"></div>
                    <div class="text-center text-2xl font-Montserrat font-semibold leading-[30px] text-[#191918]">
                        Forgot your password?
                    </div>
                    <div class="text-center font-Montserrat leading-[24px]">
                        No worries! Resetting your password is a breeze. Just enter the email
                        you registered with in E-RIHS
                    </div>
                </div>
                <div class="flex flex-col justify-between gap-4">
                    <div class="flex flex-col gap-1 items-start">

                        @if (session('status'))
                            <div class="mb-4 font-medium text-sm text-green-600">
                                {{ session('status') }}
                            </div>
                        @endif
                        <x-validation-errors class="mb-4" />
                    </div>
                    <div class="flex flex-col gap-1 items-start">
                        <div class="flex flex-row gap-1 w-12 items-center">
                            <div class="text-sm font-Montserrat font-semibold leading-[20px] text-[#374151] w-12">
                                Email
                            </div>
                            <div
                                class="text-sm font-Montserrat font-semibold leading-[20px] text-[#b4173a] w-1 shrink-0">
                                *
                            </div>
                        </div>
                        <input type="email"
                            class="border-solid border-[#d1d5db] shadow-[0px_1px_3px_0px_rgba(0,_0,_0,_0.07)] self-stretch flex flex-col h-10 shrink-0 px-3 py-2 border rounded-lg"
                            name="email" required>
                    </div>
                    <button type="submit"
                        class="shadow-[0px_1px_3px_0px_rgba(0,_0,_0,_0.16)] bg-[#2d4547] text-[#F9FAFB] flex flex-col justify-center h-10 shrink-0 items-center rounded-lg">
                        Send a link to reset
                    </button>
                    <div class="text-center text-xs font-Montserrat leading-[16px] text-[#374151]">
                        Remembered your password?
                        <a href="/login" class="underline! text-[#0c5af0] contents">Try again</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

</x-login_common>
