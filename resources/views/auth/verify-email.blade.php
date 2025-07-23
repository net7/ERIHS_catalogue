<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body class="flex flex-col items-center justify-center h-screen bg-white">
    <div class="border-solid border-[#e5e7eb] bg-[#f9fafb] self-stretch flex flex-col justify-center pl-8 h-16 shrink-0 items-start border-t-0 border-b border-x-0 fixed w-full top-0">
        <img src="https://file.rendit.io/n/4X0BRij0hTxEHgcTwPeD.png" class="min-h-0 min-w-0" />
    </div>
    <div class="w-full max-w-[600px] px-4 flex flex-col items-center">
        <div id="this" class="flex flex-col justify-center items-center gap-4 my-16">
            <img src="https://file.rendit.io/n/9DkmOZOIFkpQYqft8mTD.svg" class="w-16" />
            <div class="text-center text-3xl font-['Montserrat'] font-bold leading-[48px] text-[#111827]">
                Thank you for registering!
                <br />
                The next step is to activate your account
            </div>
            <div class="text-center text-lg font-['Montserrat'] leading-[26px] text-[#374151]">
                An email with an activation link has been sent to you. Please use it to
                activate your account. <br />
                The activation code expires in 24 hours.
            </div>
            <form method="POST" action="{{ route('verification.send') }}" class="w-full">
                @csrf
                <div class="flex justify-center">
                    <x-button type="submit">
                        {{ __('Resend Verification Email') }}
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
