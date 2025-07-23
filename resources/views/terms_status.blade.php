@php
    $accepted = data_get($this->data, 'terms_of_service', false);
@endphp

<div class="text-sm {{ $accepted ? 'text-green-700' : 'text-red-700' }}">
    {!! $accepted 
        ? '<span style="display: inline-block; font-size: 29px; color: #24a339;">☑</span> <strong>Terms of Reference accepted.</strong>'
        : '<span style="display: inline-block; font-size: 29px; color: #d01b1b;">☒</span> <strong>Terms of Reference not accepted yet. You\'ll be able to accept them after you complete your profile.</strong> '
        !!}
</div>