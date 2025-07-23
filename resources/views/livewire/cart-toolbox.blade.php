<p>
    @livewire(
        'tool-box', 
        [ 
            'tool' => $getRecord(),
            'hideOuterBorder' => true,
            'removeButtonText' => 'Remove',
            'showFavouritesInteractionButtons' => false,
            'showViewDetailsButton' => true,
        ],
        key($recordKey),
    )
</p>