<p>
    @livewire(
        'service-box',
        [
            'service' => $getRecord(),
            'hideOuterBorder' => true,
            'removeButtonText' => 'Remove',
            'showFavouritesInteractionButtons' => false,
            'showViewDetailsButton' => true,
        ],
        key($recordKey),
    )
</p>
