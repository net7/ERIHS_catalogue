<?php

namespace App\Filament\Pages\Auth;

use App\Filament\Pages\ProfileCommons;
use App\Filament\Resources\OrganizationResource;
use App\Models\Organization;
use App\Models\User;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Filament\Support\Enums\Alignment;
use Spatie\Permission\Models\Role;


class EditProfile extends BaseEditProfile implements HasForms
{
    protected static string $view = 'filament.pages.profile';

    public $cancel_button_url = 'dashboard';

    protected static string $layout = 'components.layouts.app';

    /**
     * @var array<string, string>
     */
    public array $formData;

    public $user;

    public ?array $data = [];

    protected function getRedirectUrl(): string
    {
        return '/dashboard';
    }

    public function getFormStatePath(): ?string
    {
        return 'data';
    }

    public function getFormActionsAlignment(): string|Alignment
    {
        return Alignment::Right;
    }


    public function form(Form $form): Form
    {
        return $form->schema(
            [
                Tabs::make('Profile')
                    ->tabs([
                        Tab::make('Profile')
                            ->schema([
                                Fieldset::make('My details')
                                    ->schema(
                                        ProfileCommons::myDetailsFieldsSchema()
                                    )
                                    ->columns(3),
                                Fieldset::make('Institutional information')
                                    ->schema(
                                        ProfileCommons::myInstitutionalInformationSchema()
                                    )
                                    ->columns(3),
                                Fieldset::make('Background')
                                    ->schema(
                                        ProfileCommons::myAccountSettingsSchema()
                                    )
                                    ->columns(1),
                            ]),
                        Tab::make('Reviewer details')
                            ->hidden(fn (\Filament\Forms\Get $get): bool => !auth()->user()->hasRole(User::REVIEWER_ROLE))
                            ->schema([
                                Fieldset::make('Reviewer details')
                                    ->schema(
                                        ProfileCommons::myReviewerDetails()
                                    )->columns(1),
                            ]),
                        Tab::make('Organization details')
                            ->hidden(fn (\Filament\Forms\Get $get): bool => !auth()->user()->hasRole(User::SERVICE_MANAGER))
                            ->schema([
                                Repeater::make('organizationUsers')
                                    ->label('Organizations')
                                    ->relationship()
                                    ->columnSpan('full')
                                    ->required()
                                    ->disabled()
                                    ->schema([
                                        Select::make('organization_id')
                                            ->relationship(name: 'organization', titleAttribute: 'name')
                                            ->label('Organizations')
                                            ->options($this->getUser()->organizations()->pluck('name', 'organization_id'))
                                            ->live()
                                            ->required()
                                            ->distinct()
                                            ->searchable()
                                            // ->createOptionForm(OrganizationResource::formSchema())
                                            // ->createOptionModalHeading('Create Organization')
                                            // ->editOptionForm(OrganizationResource::formSchema())
                                            // ->editOptionModalHeading(function ($state) {
                                                // return 'Edit organization ' . Organization::find($state)->name;
                                            // })
                                    ])
                                    ->addActionLabel('Add organization'),
                                Placeholder::make('In order to be added to an organization, please contact that organization contact person')
                            ])
                    ])
            ]
        )
            ->model($this->getUser())
            ->statePath('data');
    }

    // public function getCancelButtonUrlProperty(): string
    // {
    //     return route('dashboard');
    // }

}
