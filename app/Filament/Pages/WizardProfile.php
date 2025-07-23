<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class WizardProfile extends Page
{
    use InteractsWithForms;
    protected static string $view = 'filament.pages.wizard-profile';
    protected static bool $shouldRegisterNavigation = false;
    public $user;
    public ?array $data = [];

    public $wasInsertingProposal = false;

    public function getHeading(): string
    {
        return '';
    }

    // public function getFormStatePath(): ?string
    // {
    //     return 'data';
    // }
    // public function mount(): void
    // {
    //     if (session()->has('was_creating_proposal')){
    //         $this->wasInsertingProposal = true;
    //     }
    //     $this->user = Auth::user();

    //     $this->form->fill([
    //         'surname' => $this->user->surname,
    //         'name' => $this->user->name,
    //         'city' => $this->user->city,
    //         'country' => $this->user->country,
    //         'nationality' => $this->user->nationality,
    //         'birth_year' => $this->user->birth_year,
    //         'gender' => $this->user->gender,
    //         'home_institution' => $this->user->home_institution,
    //         'institution_status_code' => $this->user->institution_status_code,
    //         'institution_country' => $this->user->institution_country,
    //         'job' => $this->user->job,
    //         'institution_address' => $this->user->institution_address,
    //         'institution_city' => $this->user->institution_city,
    //         'academic_background' => $this->user->academic_background,
    //         'position' => $this->user->position,
    //         'mailing_address' => $this->user->mailing_address,
    //         'email' => $this->user->email,
    //         'office_phone' => $this->user->office_phone,
    //         'status' => is_null($this->user->email_verified_at) ? false : true,
    //         // 'roles' => $this->user->getRoleNames()->toArray(),
    //         'short_cv' => $this->user->short_cv,

    //     ]);
    // }

    public function mount(): void
    {
        if (session()->has('was_creating_proposal')){
            $this->wasInsertingProposal = true;
        }
        $this->user = Auth::user();
        $this->form->fill($this->user->toArray());
    }

    public function form(Form $form): Form
    {

        $formSchema = ProfileCommons::myDetailsFieldsSchema([
            Placeholder::make('Provide your personal details')
            ->content(new HtmlString('<span class="text-xs font-light">Please take a moment to insert your details below and proceed to the next step to build your profile.</span>'))
            ->columnSpan('full')
        ]);

        // ORCID provides useless email address for people without verified email, we don't want to use them
        // we empty the field so the user is forced to insert a valid email address
        if (str_ends_with($this->user->email, '@orcid')){
            $this->user->email = '';
        }


        return $form->schema([
            Wizard::make([
                Step::make('Your details')
                    ->description(__('Provide your personal details'))
                    ->schema([
                        Fieldset::make()
                            ->schema(
                                $formSchema
                            )
                            ->columns(3),
                            Placeholder::make("")
                            ->content(new HtmlString("<div class='text-xs font-light text-center'>You'll be able to change your details at later stage in your profile settings.</div>"))
                            // ->columnSpan('full')
                            ,
                    ]),
                Step::make('Institutional information')
                    ->description(__('Provide your Institutional information'))
                    ->schema([
                        Fieldset::make()
                            ->schema(

                                ProfileCommons::myInstitutionalInformationSchema([
                                    Placeholder::make('Provide your institutional details')
                                    ->content(new HtmlString('<span class="text-xs font-light">Please take a moment to insert your details below and proceed to the next step to build your profile.</span>'))
                                    ->columnSpan('full'),
                                ])
                             )
                            ->columns(3),
                    ]),
                Step::make('Curriculum Vitae')
                    ->description(__('Provide a short Curriculum Vitae'))
                    ->schema([
                        Fieldset::make()
                            ->schema(
                                ProfileCommons::myAccountSettingsSchema()
                            )
                            ->columns(1),
                    ]),
            ])
                ->skippable()
                ->submitAction(new HtmlString('<button type="submit" class="filament-button filament-button-size-sm inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2rem] px-3 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700">Submit</button>'))

        ])
        ->statePath('data')
        ->model($this->user);
    }

    // protected function getFormSchema(): array
    // {
    //     return [
    //         Wizard::make([
    //             Step::make('Your details')
    //                 ->description(__('Provide your personal details'))
    //                 ->schema([
    //                     Fieldset::make()
    //                         ->schema(
    //                             ProfileCommons::myDetailsFieldsSchema([
    //                                 Placeholder::make('Provide your personal details')
    //                                 ->content(new HtmlString('<span class="text-xs font-light">Please take a moment to insert your details below and proceed to the next step to build your profile.</span>'))
    //                                 ->columnSpan('full')
    //                             ]),
    //                         ),
    //                         Placeholder::make("")
    //                         ->content(new HtmlString("<div class='text-xs font-light text-center'>You'll be able to change your details at later stage in your profile settings.</div>"))
    //                         // ->columnSpan('full')
    //                         ,
    //                 ]),
    //             Step::make('Institutional informations')
    //                 ->description(__('Provide your Institutional informations'))
    //                 ->schema([
    //                     Fieldset::make()
    //                         ->schema(

    //                             ProfileCommons::myInstitutionalInformationSchema([
    //                                 Placeholder::make('Provide your institutional details')
    //                                 ->content(new HtmlString('<span class="text-xs font-light">Please take a moment to insert your details below and proceed to the next step to build your profile.</span>'))
    //                                 ->columnSpan('full'),
    //                             ])
    //                          )
    //                         ->columns(3),
    //                 ]),
    //             Step::make('Your attachments')
    //                 ->description(__('Attach your Curriculum Vitae'))
    //                 ->schema([
    //                     Fieldset::make()
    //                         ->schema(
    //                             ProfileCommons::myAccountSettingsSchema()
    //                         )
    //                         ->columns(1),
    //                 ]),
    //         ])
    //             ->skippable()
    //             ->submitAction(new HtmlString('<button type="submit" class="filament-button filament-button-size-sm inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2rem] px-3 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700">Submit</button>'))
    //     ];
    // }

    public function submit()
    {
        if ($this->user->update($this->form->getState()) > 0) {
            $this->user->update(['first_login' => false]);
            if ($this->wasInsertingProposal){
                // Redirect(route('proposal'))->with('was_creating_proposal','true');
                return redirect()->to(route('proposal'))->with('was_creating_proposal','true');


            } else {
                Notification::make()
                    ->title('Saved successfully')
                    ->success()
                    ->duration(5000)
                    ->send();
                Redirect(route('dashboard'));
            }
        } else {
            Notification::make()
                ->title('Something went wrong')
                ->danger()
                ->duration(5000)
                ->send();
        }
    }

    public function goToDashboard(){

        if($this->user->hasRole(User::REVIEWER_ROLE)) {
            if($this->user->terms_of_service) {
                return redirect()->to(route('dashboard'));
            } else {
                return redirect()->to(route('reviewer-terms-and-conditions'));
            }
        } else {
            return redirect()->to(route('dashboard'));
        }
    }


    public function render(): View
    {

        // if ($this->wasInsertingProposal){
        if (session()->has('was_creating_proposal')){
            Notification::make()
                ->title('In order to submit new proposals you must complete your profile first.')
                ->body('Please fill in the form in all its parts.')
                ->warning()
                ->send();
        }
        $view = view('filament.pages.wizard-profile');
        // $view->layout('filament::components.layouts.base', [
        //     'title' => __('Profile'),
        //         ]);
        return $view;

    }
}
