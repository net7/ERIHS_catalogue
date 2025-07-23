<?php

namespace App\Filament\Resources\CommonProposalResource\Pages;

use App\Livewire\CreateProposal;
use App\Models\Proposal;
use App\Models\User;
use App\Services\UserService;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Forms\Form;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Kenepa\ResourceLock\Resources\Pages\Concerns\UsesResourceLock;
class EditProposal extends EditRecord
{
    use UsesResourceLock;
    use HasPageSidebar;

    protected static ?string $breadcrumb = "Edit proposal";
    protected static ?string $title = "Edit proposal";


    protected function getFormActions(): array
    {
        $user = Auth::user();
        if ($user->hasRole(User::ADMIN_ROLE) || $user->hasRole(User::HELP_DESK_ROLE)) {
            return parent::getFormActions();
        }
        return [];
    }

    public function form(Form $form): Form
    {
        $user = Auth::user();
        $proposal = Proposal::find($this->record->id);

        return $form
            ->schema([
                ...CreateProposal::getProposalFormSchema($this->record->id, false, true),
            ])
            ->columns(1)
            ->disabled(!UserService::canUserEditProposal($user, $proposal));
    }
}
