<p>
    @livewire(
        'tool-box', 
        [ 
            'tool' => $getRecord(),
            'hideOuterBorder' => true,
            'removeButtonText' => 'Remove',
            'showFavouritesInteractionButtons' => true,
            'showViewDetailsButton' => false,
        ],
        key('tool-' . $getRecord()->id)
    )
</p>