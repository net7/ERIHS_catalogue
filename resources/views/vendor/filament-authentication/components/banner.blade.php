@if(app('impersonate')->isImpersonating())
<style>
html, body {
    margin-top: 20px;
}
body
@media print {
    html {
        margin-top: 0;
    }
}
</style>
@php
$impersonating = Filament\Facades\Filament::getUserName(auth()->user());
@endphp
<div
    id="impersonating-banner"
    class="print:hidden  absolute h-10 top-0 w-full flex items-center content-center justify-center text-gray-800"
    >
    <div>
        {{ __('filament-authentication::filament-authentication.text.impersonating') }} <strong>{{ $impersonating }}</strong>
        <a href="{{ route('filament-authentication.stop.impersonation') }}"><strong>{{ __('filament-authentication::filament-authentication.text.impersonating.end') }}</strong></a>
    </div>

</div>
@endIf
