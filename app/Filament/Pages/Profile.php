<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

// use Phpsa\FilamentAuthentication\Pages\Profile as PagesProfile;

// class Profile extends PagesProfile
class Profile
{
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    // protected static ?string $navigationGroup = 'user';

    protected static string $view = 'filament.pages.profile';

    protected static bool $shouldRegisterNavigation = false;

    /**
     * @var array<string, string>
     */
    public array $formData;

    // use InteractsWithForms;

    public $user;

    public function mount(): void
    {
        // $this->user = Auth::user();
        // $this->user = Filament::auth()->user();
        $this->user = $this->getFormModel();
        $this->form->fill([
            'surname' => $this->user->surname,
            'name' => $this->user->name,
            'city' => $this->user->city,
            'country' => $this->user->country,
            'nationality' => $this->user->nationality,
            'birth_year' => $this->user->birth_year,
            'gender' => $this->user->gender,
            'home_institution' => $this->user->home_institution,
            'institution_status_code' => $this->user->institution_status_code,
            'institution_country' => $this->user->institution_country,
            'job' => $this->user->job,
            'institution_address' => $this->user->institution_address,
            'institution_city' => $this->user->institution_city,
            'academic_background' => $this->user->academic_background,
            'position' => $this->user->position,
            // 'mailing_address' => $this->user->mailing_address,
            'email' => $this->user->email,
            'office_phone' => $this->user->office_phone,
            'status' => is_null($this->user->email_verified_at) ? false : true,
            'roles' => $this->user->getRoleNames()->toArray(),
            'short_cv' => $this->user->short_cv,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
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
                    // Tab::make('Account settings')
                    //     ->schema([
                    //         Fieldset::make()
                    //             ->schema([
                    //                 Toggle::make('email_verified_at')
                    //                     ->label(
                    //                         function ($state)  {
                    //                             if($state){
                    //                                 return __('Validated');
                    //                             } else {
                    //                                 return __('Not validated');
                    //                             }
                    //                         }
                    //                     )
                    //                     ->dehydrated(false)
                    //                     ->onColor('success')
                    //                     ->offColor('danger')
                    //                     ->inline(false)
                    //                     ->live()
                    //                     ->disabled()
                    //                     ,
                    //                 TextInput::make('email')
                    //                     ->label(__('E-mail address'))
                    //                     ->email()
                    //                     ->disabled()
                    //                     ->label(__('If you have a different email address than the one you used to log in,
                    //                         kindly provide an additional email address.')),
                    //             ])
                    //             ->columns(1),
                    //         Fieldset::make()
                    //             ->schema([
                    //                 Select::make('roles')
                    //                     ->label(__('Assigned roles'))
                    //                     ->multiple()
                    //                     ->options(Role::all()->pluck('name'))
                    //                     ->placeholder('')
                    //                     ->disabled()
                    //             ])
                    //             ->columns(1)
                    //     ]),
                    // Tab::make('Documents')
                    //     ->schema([
                    //         // ...
                    //     ]),
                ])
        ];
    }

    public function getCancelButtonUrlProperty(): string
    {
        return route('dashboard');
    }

    public function submit(): void
    {
        if ($this->user->update($this->form->getState()) > 0) {
            Notification::make()
                ->title('Saved successfully')
                ->success()
                ->duration(5000)
                ->send();
            // Redirect('/dashboard');
        } else {
            Notification::make()
                ->title('Something went wrong')
                ->danger()
                ->duration(5000)
                ->send();
        }
    }
    protected function getBreadcrumbs(): array
    {
        return [
            url()->current() => 'Profile',
        ];
    }

    // public function render(): View
    // {
    //       /* -- optional: this line changes the default width for this view only -- */
    //     //   config(['filament-breezy.auth_card_max_w' => '4xl']);
    //       /* ---- */
    //       $view = view(self::$view);
    //     //   $view->layout('filament::components.layouts.app', [
    //         $view->layout('filament::components.layouts.base', [
    //             'title' => __('Profile'),
    //       ]);
    //       return $view;
    //     // return view(self::$view);
    // }
}
