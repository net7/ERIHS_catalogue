<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body class="h-screen overflow-hidden flex items-center justify-center bg-white">
    <div
        class="border-solid border-[#e5e7eb] bg-[#f9fafb] self-stretch flex flex-col justify-center pl-8 h-16 shrink-0 items-start border-t-0 border-b border-x-0 fixed top-0 left-0 right-0">
        <img src="https://file.rendit.io/n/3zrT2fTZ8z1TyyUgq0ht.png" class="min-h-0 min-w-0">
    </div>
    <div class="flex flex-col gap-2 w-full md:w-1/2 items-center mt-2">
        <div class="self-stretch flex flex-col justify-between gap-4">
            <img src="https://file.rendit.io/n/RN37lQm03y6ukA56zzWc.svg" class="min-h-0 min-w-0 self-center w-16">
            <div class="text-center text-3xl font-['Montserrat'] font-bold leading-[48px] text-[#111827]">
                Great your account has been verified. <br>
                Please complete your profile
            </div>
            <div class="text-center text-xl font-['Montserrat'] leading-[32px] text-[#374151]">
                To unlock the full potential of our services we kindly request you to
                complete your information. By doing so, you'll gain access service
                explorations, request submissions, and proposal opportunities.
            </div>
        </div>
        <div class="flex flex-col md:flex-row gap-3 w-full md:w-2/5 items-center justify-center mt-10">
            <button
                class="flex px-4 py-2 justify-center items-center gap-1 rounded-md border border-primary-600 bg-grey-50 shadow-sm">
                <span class="whitespace-nowrap">Skip for now</span>
            </button>

            <button class="flex px-4 py-2 justify-center items-center gap-1 rounded-md bg-primary-600 shadow-sm">
                <span class="text-white whitespace-nowrap">Complete my profile</span>
            </button>
        </div>
    </div>
</body>

</html>
