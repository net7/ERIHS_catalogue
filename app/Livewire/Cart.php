<?php

namespace App\Livewire;

use App\Models\Service;
use App\Services\ERIHSCartService;
use App\Services\ProposalService;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class Cart extends Component implements HasForms, HasInfolists, HasTable
{
    use InteractsWithForms;
    use InteractsWithInfolists;
    use InteractsWithTable;

    public $tools;

    private $toolsIds;

    protected $listeners = ['refreshCart' => 'resetTable'];

    public function mount()
    {
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Service::whereIn('id', ERIHSCartService::getItemsIds()))
            ->columns([
                View::make('livewire.cart-servicebox'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->contentGrid(['sm' => 1])
            ->emptyStateIcon('heroicon-o-clipboard-document-list')
            ->emptyStateHeading('It seems like you haven\'t planned to submit any proposals just yet')
            ->emptyStateDescription('Browse through the catalog, begin saving items to your collection,
                                    and then select which ones you\'d like to include in your proposal.')
            ->paginated(false);
    }

    public function cartInfolist(Infolist $infolist): Infolist
    {

        $itemCount = ERIHSCartService::getItemsCount();

        $infolistState = [
            'serviceNumber' => new HtmlString(
                '<div class="text-lg">
               <span class="font-bold ">' .
                    $itemCount .
                    '  </span>
               <span class="font-thin">Service(s)</span>
             </div>'
            ),
        ];


        $infolistState['lockedText'] = new HtmlString(
            '
                <div class="text-sm text-gray-400 leading-tight">
                    There are no available open calls yet. You could anyway save a proposal as a
                    draft waiting for a new call to be open

                </div>
                '
        );

        $infolistState['lockedTextSubmitted'] = new HtmlString(
            '
                <div class="text-sm text-gray-400 leading-tight">
                    You already have a submitted proposal.
                </div>
                <div class="inline text-sm text-gray-600 leading-tight">
                    <b><a href="/dashboard/my-proposals">Manage it</a></b>
                </div>
                '
        );

        $infolistState['locked'] = true; // set to null to hide

        $infolistState['draftText'] = new HtmlString(
            '
            <div class="text-sm leading-tight ">
                This proposal is in draft. You won\'t be able to create other proposals until
                    you complete and submit this proposal.
            </div>
            '
        );
        $infolistState['draft'] = true;

        $infolistState['missedPostAccessDutiesDeadline'] = new HtmlString(
            '
                <div class="text-sm text-gray-400 leading-tight">
                    You can no longer create a proposal as you have not entered the post access reports of your last proposal
                </div>'
        );
        $infolistState['locked'] = true; // set to null to hide

        $canSubmitProposal = ProposalService::canSubmitProposal();
        $grid = Grid::make(8)->schema([

            Actions::make([
                Action::make('creteProposal')
                    ->url(fn (): string => route('proposal'))
                    ->label('Write a Proposal')
            ])
                ->fullWidth()
                ->columnSpanFull()
        ]);
        if (!$canSubmitProposal['can_open']) {
            switch ($canSubmitProposal['motivation']) {
                case 'no_open_calls':
                    $grid = Grid::make(8)->schema([
                        IconEntry::make('locked')
                            ->label('')
                            ->icon('heroicon-o-lock-closed')
                            ->columnSpan(1),

                        TextEntry::make('lockedText')
                            ->label('')
                            ->columnSpan(7),
                    ]);
                    break;
                case 'proposal_already_opened':
                    $grid = Grid::make(8)->schema([
                        IconEntry::make('locked')
                            ->label('')
                            ->icon('heroicon-o-lock-closed')
                            ->columnSpan(1),

                        TextEntry::make('lockedTextSubmitted')
                            ->label('')
                            ->columnSpan(7),
                    ]);
                    break;
                case 'proposal_in_draft':
                    $grid = Grid::make(8)->schema([
                        IconEntry::make('draft')
                            ->label('')
                            ->icon('heroicon-o-clock')
                            ->color('warning')
                            ->columnSpan(1),

                        TextEntry::make('draftText')
                            ->label('')
                            ->color('warning')
                            ->columnSpan(7),
                        Actions::make([
                            Action::make('completeProposal')
                                ->url(fn (): string => route('proposal'))
                                ->label('Complete Proposal')
                        ])
                            ->columnSpan(7),
                    ]);
                    break;
                case 'missed_post_access_duties':
                    $grid = Grid::make(8)->schema([
                        IconEntry::make('locked')
                            ->label('')
                            ->icon('heroicon-o-lock-closed')
                            ->columnSpan(1),

                        TextEntry::make('missedPostAccessDutiesDeadline')
                            ->label('')
                            ->columnSpan(7),
                    ]);
                    break;
                case 'user_not_logged' :
                    break;
            }
        }
        return $infolist
            ->state(
                $infolistState
            )
            ->schema([
                Section::make()->schema([
                    ViewEntry::make('red')
                        ->view('components.infolist-red-ribbon')
                        ->columnSpan('full'),

                    TextEntry::make('serviceNumber')
                        ->label('')
                        ->extraAttributes(
                            ['class' => 'text-thin']
                        ),

                    $grid,

                ])
                    ->extraAttributes(
                        ['class' => 'static']
                    ),
            ]);
    }

    public function render()
    {
        return view('livewire.cart')->layout('components.layouts.app');
    }
}
