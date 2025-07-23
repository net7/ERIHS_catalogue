@php
    $tooltip = $action->getTooltip();
    $tooltipIsNotEmpty = str($tooltip)->isNotEmpty();
    $isPolling = $action->getIsPolling();
@endphp

<span @if ($isPolling) wire:poll.5s @endif>
    @if ($tooltipIsNotEmpty)
        <span x-data x-tooltip.raw="{{ $tooltip }}" class="cursor-not-allowed">
    @endif
    {{ $action->getAction() }}
    @if ($tooltipIsNotEmpty)
</span>
@endif

</span>
