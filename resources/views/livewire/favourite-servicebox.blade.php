<p>
    @livewire(
        'service-box',
        [
            'service' => $getRecord(),
            'hideOuterBorder' => true,
            'removeButtonText' => 'Remove from proposal',
            'showFavouritesInteractionButtons' => true,
            'showViewDetailsButton' => false,
        ],
        key('service-' . $getRecord()->id)
    )
</p>
