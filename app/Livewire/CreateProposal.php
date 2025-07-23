<?php

namespace App\Livewire;

use App\Enums\LearnedAboutErihs;
use App\Enums\MolabAuthorizationDroneFlight;
use App\Enums\MolabOwnershipConsent;
use App\Enums\ProposalSocialChallenges;
use App\Enums\ProposalStatus;
use App\Enums\ProposalType;
use App\Models\Proposal;
use App\Models\ProposalService as ModelsProposalService;
use App\Models\Service;
use App\Models\User;
use App\Services\CallService;
use App\Services\ERIHSCartService;
use App\Services\ProposalService;
use App\Services\ServiceService;
use App\Services\TagsService;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
// use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use Livewire\Component;

class CreateProposal extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public $proposal;

    public $name;

    public $description;

    public $image_path;

    public $errors = [];

    public $isDraft = false;
    public $editingFromBackEnd = false;

    public ?array $data = [];

    protected $listeners = [
        'refresh-form' => 'refreshForm',
        'refresh-repeater' => 'refreshProposalServiceRepeater',
    ];

    public function refreshProposalServiceRepeater()
    {
        $wizard = $this->proposalForm->getComponent('wizard');
        if ($wizard) {
            $servicesStep = $wizard->getChildComponentContainer()->getComponent('Services summary');
            if ($servicesStep) {
                $proposalServicesSection = $servicesStep->getChildComponentContainer()->getComponent('Proposal Services');
                if ($proposalServicesSection) {
                    $proposalServicesRepeater = $proposalServicesSection->getChildComponentContainer()->getComponent('proposalServices');
                    if ($proposalServicesRepeater) {
                        $proposalServicesRepeater->refresh();
                    }
                }
            }
        }
        $this->render();
    }

    public function refreshForm()
    {
        $this->updateProposalServices($this->proposal->getPlatforms());
        $this->proposalForm = $this->getForms()['proposalForm'];

        $this->render();
    }


    public function updateProposalServices($platforms)
    {


        $changed = false;
        if (!$platforms->contains('Molab')) {
            // Removing molab fields
            $this->proposal->molab_quantity = null;
            $this->proposal->molab_objects_data = null;
            $this->proposal->molab_drone_flight = null;
            $this->proposal->molab_drone_flight_file = null;
            $this->proposal->molab_drone_flight_comment = null;
            $this->proposal->molab_note = null;
            $this->proposal->molab_logistic = null;
            $this->proposal->molab_x_ray = null;
            $this->proposal->molab_x_ray_file = null;
            $changed = true;
        }

        if (!$platforms->contains('Archlab')) {
            // Removing archlab fields
            $this->proposal->archlab_type = null;
            $this->proposal->archlab_type_other = null;
            $changed = true;
        }

        if (!$platforms->contains('Fixlab')) {
            // Removing fixlab fields
            $this->proposal->fixlab_quantity = null;
            $this->proposal->fixlab_objects_data = null;
            $this->proposal->fixlab_logistic = null;
            $changed = true;
        }

        if ($changed) {
            $this->proposal->save();
        }
    }

    public function mount(): void
    {
        $user = ProposalService::getUser();

        $this->proposal = Proposal::withoutGlobalScope('PublishingScope')
            ->whereRelation('leader', 'applicant_id', $user->id)
            ->current()
            ->draftable()
            ->orderBy('id')->first() ?: new Proposal();

        $proposalFormData = ProposalService::getProposalFormData($this->proposal);

        $services = isset($this->proposal->id) ? ProposalService::getServicesForProposalFromDB($this->proposal->id) : ERIHSCartService::getItems();

        $platforms = self::getPlatforms($services);

        if (isset($this->proposal->id)) {
            $leader = $this->proposal->leader()->first();
            $proposalFormData['cv'] = $leader->short_cv;
            if ($this->proposal->status == 'draft') {
                $proposalFormData['call_id'] = CallService::getOpenCalls()->first()->id;
            } else {
                $proposalFormData['proposalServices'] = ProposalService::getItemsForProposalFromDB($this->proposal->id);
            }
        } else {
            if (ProposalService::getItemsForProposalFromCart()->isempty()) {
                Notification::make()
                    ->title('Please add services to the cart first')
                    ->warning()
                    ->send();
                redirect()->to(route('catalogue'));
                return;
            }
            $proposalFormData['proposalServices'] = ProposalService::getProposalItems();
            $proposalFormData['cv'] = $user->short_cv;
            $proposalFormData['call_id'] = CallService::getOpenCalls()->first()->id;

            $applicantProposals = [[
                'applicant_id' => $user->id,
                'email' => $user->email,
                'leader' => true,
                'alias' => false
            ]];

            $proposalFormData['applicantProposals'] = $applicantProposals;
            $this->proposal->applicantProposals = $applicantProposals;
            $this->addFormDataForTest($proposalFormData);
        }

        $this->proposalForm->fill($proposalFormData);
    }


    public static function getFieldLabels($field = null)
    {

        $labels = [
            'whom' => 'With whom did you get in touch?',
            'molab_quantity' => 'Number of objects to be analysed',
            'molab_objects_data' => 'Objects description',
            'molab_x_ray' => 'Should MOLAB X-ray based instrumentations be requested, have any national radiation protection measures (licenses/accreditations) and the duration for their request been pre-determined?',
            'molab_x_ray_file' => 'X-Ray license/accrediation files',
            'molab_object_type' => 'Type',
            'molab_object_size' => 'Size of the object',
            'molab_object_location' => 'Location',
            'molab_object_ownership' => 'Ownership',
            'molab_object_ownership_comment' => 'Specify',
            'molab_drone_flight' => 'Authorization request for drone flights (if applicable)',
            'molab_drone_flight_comment' => 'Comment on drone flight authorization',
            'molab_object_ownership_consent' => 'Ownership consent',
            'molab_object_ownership_consent_file' => 'Ownership consent file',
            'molab_logistic' => 'Outline specific logistic preparations (scaffolding etc.), risks and safety hazards (as applicable)',

            'fixlab_quantity' => 'Number of objects to be analysed',
            'fixlab_objects_data' => 'Objects description',
            'fixlab_object_type' => 'Type',
            'fixlab_number_of_measures' => 'Number of measures/processes',
            'fixlab_object_form' => 'Form/shape',
            'fixlab_object_size' => 'Size',
            'fixlab_object_temperature' => 'Temperature range for the sample environment',
            'fixlab_object_air_condition' => 'Air condition / humidity for the sample environment',
            'fixlab_object_ownership' => 'Ownership',
            'fixlab_object_preparation' => 'Preparation',
            'fixlab_object_notes' => 'Other notes',
            'fixlab_logistic' => 'Outline specific logistic preparations (scaffolding etc.), risks and safety hazards (as applicable)',

            'eu_or_national_projects_related' => 'Are any other EU or national projects related to this proposal?',
            'training_activity' => 'Is the project related to initial training (PhD) or a training activity?',

            'name_of_the_project' => 'Name of the project',
            'founded_by' => 'Funded by',
            'number_of_grant_agreement' => 'In case of a EU project please insert the Grant Agreement number',
            'training_activity_details' => 'Specify',
            'industrial_involvement' => 'Does this proposal have any industrial involvement or sponsorship?',
            'industrial_involvement_details' => 'Specify',
            'learned_about_erihs' => 'How did you learn about E-RIHS?',
            'other_details' => 'Please explain',
            'social_challenges' => 'Societal challenges',

            'terms_and_conditions' => 'I understand and consent that personal and private data herein contribute only to their reliable and
efficient processing within E-RIHS and shall be used in accordance with the EU GDPR.',
            'consent_to_videotape_and_photography' => 'I hereby grant consent for videotaping and photography during the Access, which may be
utilized by E-RIHS exclusively for communication and dissemination purposes.',
            'news_via_email' => 'I hereby consent to receiving the latest updates and news from E-RIHS via email.',

            'project_description' => 'Description of the project',
            'project_summary' => 'Project summary',
            'scientific_background' => 'Scientific background',
            'description_of_the_planned_work' => 'Description of the planned work and Analytical Methods',
            'research_questions' => 'Research questions',
            'previous_analysis' => 'Previous analysis on the item',
            'expected_achievements' => 'Expected achievements',
            'project_impacts' => 'Impact and dissemination plan',
            'data_management_plan' => 'Data Management Plan',
            'references' => 'References',
            'caption' => 'File Description',
            'resubmission_previous_proposal_number' => 'Previous proposal'
        ];

        if ($field) {
            if (!isset($labels[$field])) {
                return 'MISSING LABEL - ' . $field;
            }

            return $labels[$field];
        }
        return $labels;
    }

    public static function getArchlabSection($hasArchlab, $isDraft)
    {
        return Section::make(new HtmlString(
            html: '<span style="background-color: #F3F4F6" class="py-2 px-3 rounded-md">
                        Archlab
                    </span>'
        ))
            ->collapsible()
            ->disabled(fn() => !$hasArchlab)
            ->hidden(fn() => !$hasArchlab)
            ->schema([
                CheckboxList::make('archlab_type')
                    ->label(__('Select one or more options'))
                    ->required(!$isDraft)
                    ->columns(3)
                    ->options(
                        fn() => collect(config('app.archlab_document_types'))
                            ->mapWithKeys(function (string $item, int $key) {
                                return [$item => $item];
                            })
                    )
                    ->live(),
                Textarea::make('archlab_type_other')
                    ->label(__('Specify'))
                    ->autosize()
                    ->visible(function ($get) {
                        return in_array('Other', $get('archlab_type'));
                    })
                    ->required(function ($get) use ($isDraft) {
                        return in_array('Other', $get('archlab_type')) && !$isDraft;
                    }),
            ]);
    }

    public static function getMolabSection($hasMolab, $isDraft, $updateFiles = false)
    {
        return Section::make(
            new HtmlString(
                html: '<span style="background-color: #F3F4F6" class="py-2 px-3 rounded-md">
                            Molab
                        </span>'
            )
        )
            ->collapsible()
            ->disabled(fn() => !$hasMolab)
            ->hidden(fn() => !$hasMolab)
            ->schema([
                TextInput::make('molab_quantity')
                    ->hidden(fn() => $updateFiles)
                    ->required(!$isDraft)
                    ->rules(['numeric'])
                    ->minValue(1)
                    ->live()
                    ->label(__(self::getFieldLabels('molab_quantity'))),
                TextInput::make('molab_object_location')
                    ->hidden($updateFiles)
                    ->required(!$isDraft)
                    ->label(__(self::getFieldLabels('molab_object_location')))
                    ->helperText(
                        __('Specify the exact location in which the objects are located')
                    ),
                Repeater::make('molab_objects_data')
                    ->minItems(function ($get) use ($isDraft) {
                        return $isDraft ? 0 : ($get('molab_quantity') ? $get('molab_quantity') : 1);
                    })
                    ->maxItems(fn($get) => $isDraft ? PHP_INT_MAX : ($get('molab_quantity') ? $get('molab_quantity') : PHP_INT_MAX))
                    ->label(__(self::getFieldLabels('molab_objects_data')))
                    ->itemLabel('Molab object')
                    ->extraAttributes(['class' => 'bg-gray-100 p-3'])
                    ->hint(new HtmlString('<div>Add a description for each object. <br> Click the button and complete
                        the <br> form based on the number of objects.</div>'))
                    ->addable(!$updateFiles)
                    ->addActionLabel(__('Add physical description of an object'))
                    ->schema([
                        Select::make('molab_object_type')
                            ->hidden($updateFiles)
                            ->multiple()
                            ->dehydrated()
                            ->required(!$isDraft)
                            ->label(__(self::getFieldLabels('molab_object_type')))
                            ->options(
                                ProposalService::getMolabObjectTypes()
                            ),
                        TagsService::tagsSchemaForRepeater(
                            'molab_object_material',
                            'material',
                            'Material',
                            required: !$isDraft,
                            multiple: true,
                            searchable: true,
                            preload: false,
                            canCreate: false,
                        )
                            ->hidden($updateFiles)
                            ->dehydrated(),
                        TextInput::make('molab_object_size')
                            ->dehydrated()
                            ->hidden($updateFiles)
                            ->required(!$isDraft)
                            ->helperText('Specify the size of the object/s and the unit of measure')
                            ->label(__(self::getFieldLabels('molab_object_size'))),

                        TextInput::make('molab_object_ownership')
                            ->disabled($updateFiles)
                            ->dehydrated(true)
                            ->required(!$isDraft)
                            ->label(__(self::getFieldLabels('molab_object_ownership')))
                            ->helperText(__('Describe the ownership of the object/s: Indicate details (person, institution, etc.)')),
                        Radio::make('molab_object_ownership_consent')
                            ->required(!$isDraft)
                            ->options(MolabOwnershipConsent::options())
                            ->live()
                            ->columns(3),
                        Textarea::make('molab_object_ownership_comment')
                            ->label(__(self::getFieldLabels('molab_object_ownership_comment')))
                            ->autosize()
                            ->hidden(function ($get) {
                                return $get('molab_object_ownership_consent') != MolabOwnershipConsent::OTHER->name;
                            })
                            ->required(function ($get) use ($isDraft) {
                                return $get('molab_object_ownership_consent') == MolabOwnershipConsent::OTHER->name && !$isDraft;
                            }),
                        FileUpload::make('molab_object_ownership_consent_file')
                            ->previewable(false) // to show the actual file name in the form
                            ->hintIcon('heroicon-o-question-mark-circle')
                            ->hintIconTooltip(__('dimension'))
                            ->downloadable()
                            ->visibility('private')
                            ->hidden(function ($get) {
                                return $get('molab_object_ownership_consent') != MolabOwnershipConsent::RECEIVED->name &&
                                    $get('molab_object_ownership_consent') != MolabOwnershipConsent::REQUESTED->name;
                            })
                            ->required(function ($get) use ($isDraft) {
                                return ($get('molab_object_ownership_consent') == MolabOwnershipConsent::RECEIVED->name ||
                                    $get('molab_object_ownership_consent') == MolabOwnershipConsent::REQUESTED->name) && !$isDraft;
                            })
                            ->helperText('Please upload a file with the request or the permission received')
                            ->hintIcon(
                                'heroicon-m-question-mark-circle',
                                tooltip: 'Maximum file size: 50 Mb'
                            ),
                        Placeholder::make('molab_object_note')
                            ->label('')
                            ->visible(function ($get) {
                                return $get('molab_object_ownership_consent') == MolabOwnershipConsent::OTHER->name ||
                                    $get('molab_object_ownership_consent') == MolabOwnershipConsent::REQUESTED->name;
                            })

                            ->content(new HtmlString(
                                '
                                <div style="display: flex; align-items: center;">
                                    <span class="h-10 w-10">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="size-6">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                        </svg>
                                    </span>
                                    &nbsp;
                                    If a consent has been requested, the final file with the approved consent must be uploaded before accessing the service; otherwise, access will not be permitted
                                </div>
                                '
                            ))


                        // ->content("If a consent has been requested, the final file with the approved consent must be uploaded before accessing the service; otherwise, access will not be permitted"),
                    ])
                    ->defaultItems(1),
                Radio::make('molab_drone_flight')
                    ->required(!$isDraft)
                    ->label(__(self::getFieldLabels('molab_drone_flight')))
                    ->options(MolabAuthorizationDroneFlight::options())
                    ->live()
                    ->columns(4),
                FileUpload::make('molab_drone_flight_file')
                    ->previewable(false) // to show the actual file name in the form
                    ->dehydrated()
                    ->downloadable()
                    ->visibility('private')
                    ->hidden(function ($get) {
                        return $get('molab_drone_flight') != MolabAuthorizationDroneFlight::RECEIVED->name && $get('molab_drone_flight') != MolabAuthorizationDroneFlight::REQUESTED->name;
                    })
                    ->required(function ($get) use ($isDraft) {
                        return ($get('molab_drone_flight') == 'received' || $get('molab_drone_flight') == 'requested') && !$isDraft;
                    })
                    ->helperText('Please upload a file with the request or the permission received')
                    ->hintIcon(
                        'heroicon-m-question-mark-circle',
                        tooltip: 'Maximum file size: 50 Mb'
                    ),
                Textarea::make('molab_drone_flight_comment')
                    ->label(__(self::getFieldLabels('molab_drone_flight_comment')))
                    ->autosize()
                    ->hidden(function ($get) {
                        return $get('molab_drone_flight') != MolabAuthorizationDroneFlight::OTHER->name;
                    })
                    ->required(function ($get) use ($isDraft) {
                        return $get('molab_drone_flight') == MolabAuthorizationDroneFlight::OTHER->name && !$isDraft;
                    }),
                Placeholder::make('molab_note')
                    ->label('')
                    ->visible(function ($get) {
                        return $get('molab_drone_flight') ==  MolabAuthorizationDroneFlight::OTHER->name;
                    })
                    ->content("Please note that it is mandatory to upload the permission before access is granted"),
                Textarea::make('molab_logistic')
                    ->disabled($updateFiles)
                    ->autosize()
                    ->dehydrated(true)
                    ->label(__(self::getFieldLabels('molab_logistic')))
                    ->required(!$isDraft)
                    ->helperText(new HtmlString('Fully describe any logistic preparations necessary for access as well as any risk or hazards during the access. <br/>
                                    <span class="text-gray-900">Note that the costs incurred are at the expense of the user.</span>
                        <span class="text-gray-900"> <br> Specific security issues should be mentioned.
                        <br/>
                        Please write "Not applicable" if needed.</span>')),
                Radio::make('molab_x_ray')
                    ->label(__(self::getFieldLabels('molab_x_ray')))
                    ->boolean()
                    ->required(!$isDraft)
                    ->inline()
                    ->columns(1)
                    ->live(),
                FileUpload::make('molab_x_ray_file')
                    ->previewable(false) // to show the actual file name in the form
                    ->downloadable()
                    ->label(__(self::getFieldLabels('molab_x_ray_file')))
                    ->visibility('private')
                    ->hidden(function ($get) {
                        return $get('molab_x_ray') == false;
                    })
                    ->required(function ($get) use ($isDraft) {
                        return $get('molab_x_ray') == false && !$isDraft;
                    })
                    ->hintIcon(
                        'heroicon-m-question-mark-circle',
                        tooltip: 'Maximum file size: 50 Mb'
                    ),
            ]);
    }

    public static function getFixLabSection($hasFixlab, $isDraft)
    {
        return Section::make(
            new HtmlString(
                html: '<span style="background-color: #F3F4F6" class="py-2 px-3 rounded-md">
                        Fixlab
                    </span>'
            )
        )
            ->collapsible()
            ->disabled(fn() => !$hasFixlab)
            ->hidden(fn() => !$hasFixlab)
            ->schema([
                TextInput::make('fixlab_quantity')
                    ->required(!$isDraft)
                    ->rules(['numeric'])
                    ->live()
                    ->minValue(1)
                    ->label(__(self::getFieldLabels('fixlab_quantity'))),
                Repeater::make('fixlab_objects_data')
                    ->minItems(function ($get) use ($isDraft) {
                        return $isDraft ? 0 : ($get('fixlab_quantity') ? $get('fixlab_quantity') : 1);
                    })
                    ->maxItems(fn($get) => $isDraft ? PHP_INT_MAX : ($get('fixlab_quantity') ? $get('fixlab_quantity') : PHP_INT_MAX))
                    ->hint(new HtmlString('<div>Add a description for each object. <br> Click the button and complete
                        the <br> form based on the number of objects.</div>'))
                    ->label(__(self::getFieldLabels('fixlab_objects_data')))
                    ->addActionLabel('Add physical description of an object')
                    ->itemLabel('Fixlab object')
                    ->extraAttributes(['class' => 'bg-gray-100 p-3'])
                    ->schema([
                        Select::make('fixlab_object_type')
                            ->multiple()
                            ->required(!$isDraft)
                            ->options(ProposalService::getFixlabObjectTypes())
                            ->label(__(self::getFieldLabels('fixlab_object_type'))),
                        TagsService::tagsSchemaForRepeater(
                            'fixlab_object_material',
                            'material',
                            'Material',
                            required: !$isDraft,
                            multiple: true,
                            searchable: true,
                            preload: false,
                            canCreate: false,
                        ),
                        TextInput::make('fixlab_number_of_measures')
                            ->label(__(self::getFieldLabels('fixlab_number_of_measures')))
                            ->required(!$isDraft)
                            ->rules(['numeric'])
                            ->minValue(0),
                        TextInput::make('fixlab_object_form')
                            ->label(__(self::getFieldLabels('fixlab_object_form'))),
                        TextInput::make('fixlab_object_size')
                            ->helperText('Specify the size of the object/s and the unit of measure')
                            ->label(__(self::getFieldLabels('fixlab_object_size'))),
                        TextInput::make('fixlab_object_temperature')
                            ->label(__(self::getFieldLabels('fixlab_object_temperature'))),
                        TextInput::make('fixlab_object_air_condition')
                            ->label(__(self::getFieldLabels('fixlab_object_air_condition'))),
                        TextInput::make('fixlab_object_ownership')
                            ->required(!$isDraft)
                            ->label(__(self::getFieldLabels('fixlab_object_ownership')))
                            ->helperText(__('Describe the ownership of the object/s: Indicate details (person, institution, etc.)')),
                        Textarea::make('fixlab_object_preparation')
                            ->label(__(self::getFieldLabels('fixlab_object_preparation')))
                            ->autosize(),
                        Textarea::make('fixlab_object_notes')
                            ->label(__(self::getFieldLabels('fixlab_object_notes')))
                            ->autosize(),
                    ]),
                Textarea::make('fixlab_logistic')
                    ->label(__(self::getFieldLabels('fixlab_logistic')))
                    ->autosize()
                    ->helperText(new HtmlString('Fully describe any logistic preparations necessary for access as well as any risk or hazards during the access. <br/>
                                        <span class="text-gray-900">Note that the costs incurred are at the expense of the user.</span>
                                        <span class="text-gray-900"> <br> Specific security issues should be mentioned.
                                        <br/>
                        Pleaser write "Not applicable" if needed.</span>')),
            ]);
    }

    public static function getPlatforms($services)
    {
        $platforms = new Collection();
        foreach ($services as $service) {
            $service_platforms = $service->getPlatforms();
            foreach ($service_platforms as $sp) {
                if (!$platforms->contains($sp)) {
                    $platforms->add($sp);
                }
            }
        }
        return $platforms;
    }

    public static function getServices($proposalId)
    {
        return isset($proposalId) ? ProposalService::getServicesForProposalFromDB($proposalId) : ERIHSCartService::getItems();
    }


    public static function getArtefactSectionFormSchema($isDraft, $platforms)
    {


        $hasArchlab = $platforms->contains('Archlab');
        $hasMolab = $platforms->contains('Molab');
        $hasFixlab = $platforms->contains('Fixlab');

        $archLabSection = self::getArchlabSection($hasArchlab, $isDraft);
        $molabSection = self::getMolabSection($hasMolab, $isDraft);
        $fixlabSection = self::getFixLabSection($hasFixlab, $isDraft);

        return [
            $archLabSection,
            $molabSection,
            $fixlabSection
        ];
    }

    public static function getProposalFormSchema($proposalId, $isDraft = false, $editingFromBackEnd = false): array
    {

        $services = self::getServices($proposalId);
        $platforms = self::getPlatforms($services);

        return [
            Wizard::make()
                ->schema([
                    Step::make('Services summary')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Section::make('Objects details')
                                ->collapsible()
                                ->schema([
                                    ...static::getArtefactSectionFormSchema($isDraft, $platforms),
                                ]),
                            Section::make('Proposal Services')
                                ->schema([
                                    Repeater::make('proposalServices')
                                        ->id('proposal-services-repeater')
                                        ->relationship()
                                        ->label(new HtmlString('<strong>Selected services and scheduling access proposal</strong>'))
                                        ->minItems(1)
                                        ->defaultItems(1)
                                        ->columnSpan('full')
                                        ->schema([
                                            Forms\Components\Hidden::make('service_id'),
                                            Placeholder::make('Service')
                                                ->label('')
                                                ->content(
                                                    function (Get $get, $record): HtmlString {

                                                        $content = '
                                                            <div class="py-3 justify-start items-center inline-flex">
                                                                <div class="grow shrink basis-0 flex-col justify-start items-start inline-flex">
                                                                    <div class=" text-sm font-normal font-[\'Montserrat\'] leading-tight">
                                                                        Platform: <span class="text-gray-500">' . Service::find($get('service_id'))->getPlatforms()->implode(', ') . '</span> <br/>
                                                                        Service name:  <span class="text-gray-500"> <a target="_blank" href="' . route('service', ['id' => $get('service_id')]) . '">' .
                                                            Service::find($get('service_id'))->title . '</a></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        ';
                                                        if ($record && $record->isNotFeasible()) {
                                                            $content = '<div class="text-red-500 font-bold">The service was marked as unfeasible</div>' . $content;
                                                        }

                                                        return new HtmlString($content);
                                                    }

                                                ),

                                            TextInput::make('number_of_days')
                                                ->label('Expected duration of the access visit')
                                                ->helperText('Please write here the expected number of days/hours. If you are not able to make an educated guess,
                                                            please contact the User Helpdesk/Service Manager for assistance')
                                                ->required(!$isDraft),
                                            TextArea::make('notes')
                                                ->helperText('')
                                                ->autosize(),
                                            Hidden::make('feasible')->default(ModelsProposalService::TO_BE_DEFINED)->dehydrated(true),
                                            Hidden::make('motivation')->dehydrated(true),

                                        ])
                                        ->itemLabel(
                                            fn(array $state): ?string => Service::find($state['service_id'])->title ?? null
                                        )
                                        ->deletable(function ($state) {
                                            if (count($state) > 1) {
                                                return true;
                                            }
                                            return false;
                                        })
                                        ->deleteAction(
                                            callback: fn(Action $action) =>
                                            $action->requiresConfirmation()
                                                ->action(function (array $arguments, Repeater $component, $livewire): void {
                                                    $items = $component->getState();
                                                    unset($items[$arguments['item']]);
                                                    $component->state($items);
                                                    if (isset($livewire->proposal)) {
                                                        $livewire->saveAsDraft();
                                                    }
                                                })

                                        )
                                        ->addAction(
                                            fn(Action $action) =>
                                            $action->form([
                                                Select::make('service')
                                                    ->options(ServiceService::getServicesForProposals()->pluck('title', 'id'))
                                                    ->searchable(),
                                            ])->action(function (Action $action, $state, Form $form, $livewire, $component, $record): void {
                                                $serviceId = $action->getFormData()['service'];
                                                $service = Service::find($serviceId);
                                                if ($service) {
                                                    $items = $component->getState();
                                                    // Check if service is already added
                                                    $ps = null;
                                                    if (!collect($items)->contains('service_id', $serviceId)) {
                                                        if (!isset($livewire->proposal)) {
                                                            // we're editing in panel
                                                            $record->services()->attach($serviceId);
                                                            $ps = $record->proposalServices()->where('service_id', $serviceId)->first();
                                                            $proposalId = $record->id;
                                                        } else if ($livewire->proposal->exists) {
                                                            $livewire->proposal->services()->attach($serviceId);
                                                            $ps = $livewire->proposal->proposalServices()->where('service_id', $serviceId)->first();
                                                            $proposalId = $livewire->proposal->id;
                                                        }
                                                        if ($ps) {
                                                            $items['record-' . $ps->id] = [
                                                                'proposal_id' => $proposalId,
                                                                'service_id' => $ps->service_id,
                                                                'first_choice_start_date' => null,
                                                                'first_choice_end_date' => null,
                                                                'second_choice_start_date' => null,
                                                                'second_choice_end_date' => null,
                                                                'number_of_days' => null,
                                                                'feasible' => \App\Models\ProposalService::TO_BE_DEFINED,
                                                                'motivation' => null,
                                                                'created_at' => '2024-10-24T14:20:36.000000Z',
                                                                'updated_at' => '2024-10-24T14:20:36.000000Z',
                                                                'access' => null,
                                                                'scheduled_date' => null,
                                                                'notes' => null,
                                                                'Service' => null,
                                                            ];
                                                        } else {
                                                            $items[] = [
                                                                "service_id" => $serviceId,
                                                                "Service" => null,
                                                                "number_of_days" => null,
                                                                "notes" => null,
                                                            ];
                                                        }

                                                        $component->state($items);
                                                        if (isset($livewire->proposal)) {
                                                            $livewire->saveAsDraft();
                                                        }

                                                        Notification::make()
                                                            ->title('Service added successfully')
                                                            ->success()
                                                            ->send();
                                                    } else {
                                                        Notification::make()
                                                            ->title('Service already added')
                                                            ->warning()
                                                            ->send();
                                                    }
                                                } else {
                                                    Notification::make()
                                                        ->title('Service not found')
                                                        ->danger()
                                                        ->send();
                                                }
                                            })
                                        )
                                        ->collapsible()
                                        ->addActionLabel('Add Services'),
                                ])
                        ]),
                    Step::make('Proposal & workplan')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Section::make('Project')
                                ->columns(4)
                                ->schema([
                                    TextInput::make('call_id')
                                        ->hidden()
                                        ->dehydratedWhenHidden(),
                                    Textarea::make('name')
                                        ->label('Title of the project proposal')
                                        ->autosize()
                                        ->columnSpanFull()
                                        ->required(!$isDraft)
                                        ->maxLength(200)
                                        ->helperText(__('Insert project title (max 200 characters)')),

                                    TextInput::make('acronym')
                                        ->columnSpanFull()
                                        ->label('Project proposal acronym')
                                        ->maxLength(25)
                                        ->helperText('Insert project acronym (max 25 characters)')
                                        ->required(!$isDraft),

                                    Radio::make('type')->label('Type of proposal')
                                        ->columnSpanFull()
                                        ->inline()
                                        ->required(!$isDraft)
                                        ->options(ProposalType::options())
                                        ->live()
                                        ->helperText(
                                            new HtmlString(
                                                'Select "' . ProposalType::LONG_TERM_PROJECT->value .
                                                    '" if the proposal is a continuation of a previously successful E-RIHS project,
                                        and you wish to submit a complementary or conclusive proposal.' . '<br/>' .
                                                    ' Choose "' . ProposalType::RESUBMISSION->value .
                                                    '" if the proposal was previously submitted in an E-RIHS call but was unsuccessful,
                                        and the User Helpdesk recommends revising and resubmitting it.'
                                            )
                                        )
                                        ->afterStateUpdated(
                                            function ($state, callable $set) {
                                                $set('resubmission_previous_proposal_number', null);
                                                $set('continuation_motivation', null);
                                            }
                                        ),

                                    Select::make('resubmission_previous_proposal_number')
                                        ->columnSpan(2)
                                        ->label(__(self::getFieldLabels('resubmission_previous_proposal_number')))
                                        ->options(ProposalService::mySubmittedProposalWithoutDrafts()->pluck('name', 'id'))
                                        ->hidden(
                                            fn(\Filament\Forms\Get $get): bool => ($get('type') != ProposalType::LONG_TERM_PROJECT->name
                                                && $get('type') != ProposalType::RESUBMISSION->name)
                                        )
                                        ->searchable()
                                        ->preload()
                                        ->live(),

                                    Placeholder::make('related_project')
                                        ->label('')
                                        ->columnSpan(2)
                                        ->content(fn(Get $get): HtmlString => new HtmlString(
                                            '<div class="pt-6 justify-start items-center inline-flex">
                                                <div class="pt-3 grow shrink basis-0 flex-col justify-start items-start inline-flex">
                                                    <div class="text-gray-500 text-sm font-normal font-[\'Montserrat\'] leading-tight">
                                                        <a target="_blank" href="/dashboard/proposals/' . $get('resubmission_previous_proposal_number') . '/general-info">' .
                                                // \App\Models\Proposal::find($get('resubmission_previous_proposal_number'))?->name .
                                                'View proposal' .
                                                '   <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="pb-1.5 size-6 w-5 h-6 inline-block">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                                                </svg>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>'
                                        ))
                                        ->columns(1)
                                        ->hidden(fn(\Filament\Forms\Get $get): bool => (($get('type') != ProposalType::LONG_TERM_PROJECT->name
                                            && $get('type') != ProposalType::RESUBMISSION->name)) || empty($get('resubmission_previous_proposal_number'))),


                                    Textarea::make('continuation_motivation')
                                        ->label('Comments')
                                        ->columnSpanFull()
                                        ->autosize()
                                        ->hint('Max 300 words')
                                        ->helperText(function (\Filament\Forms\Get $get): string {
                                            if ($get('type') == ProposalType::RESUBMISSION->name) {
                                                return 'Please provide a brief description of what has been updated or changed';
                                            }
                                            if ($get('type') == ProposalType::LONG_TERM_PROJECT->name) {
                                                return 'Please specify the scientific motivation for continuing the work';
                                            }
                                        })
                                        ->required(!$isDraft)
                                        ->hidden(fn(\Filament\Forms\Get $get): bool => $get('type') != ProposalType::LONG_TERM_PROJECT->name && $get('type') != ProposalType::RESUBMISSION->name),
                                ]),
                            Section::make('Services managers')
                                ->schema([
                                    Radio::make('providers_contacted')
                                        ->label('Did you contact the user helpdesk?')
                                        ->helperText('The User Group Leader is strongly encouraged to contact the User Helpdesk before submitting the proposal. This increases the chance to submit a feasible proposal')
                                        ->boolean()
                                        ->required(!$isDraft)
                                        ->inline(),
                                    Radio::make('facility_contacted')
                                        ->label('Did you get in touch with the service manager persons?')
                                        ->boolean()
                                        ->required(!$isDraft)
                                        ->inline()
                                        ->live(),

                                    TextInput::make('whom')
                                        ->label(__(self::getFieldLabels('whom')))
                                        ->hidden(fn(\Filament\Forms\Get $get): bool => ($get('facility_contacted') != true && empty($get('facility_contacted')))),
                                ]),
                            Section::make('Detailed scientific description of the project')
                                ->schema([
                                    TextArea::make('project_summary')
                                        ->label(__(self::getFieldLabels('project_summary')))
                                        ->maxLength(16777215)
                                        ->rules(ProposalService::maxWordsRule(300))
                                        ->hint(function () {
                                            return ProposalService::maxWords(300);
                                        })
                                        ->required(!$isDraft)
                                        ->autosize(),

                                    TextArea::make('scientific_background')
                                        ->label(__(self::getFieldLabels('scientific_background')))
                                        ->maxLength(16777215)
                                        ->rules(ProposalService::maxWordsRule(500))
                                        ->autosize()
                                        ->hint(function () {
                                            return ProposalService::maxWords(500);
                                        })
                                        ->required(!$isDraft)
                                        ->hintIcon('heroicon-o-question-mark-circle')
                                        ->hintIconTooltip(__('A short description of any relevant scientific research which led to or supports this project')),

                                    Textarea::make('description_of_the_planned_work')
                                        ->maxLength(16777215)
                                        ->required(!$isDraft)
                                        ->rules(ProposalService::maxWordsRule(600))
                                        ->label(__(self::getFieldLabels('description_of_the_planned_work')))
                                        ->autosize()
                                        ->hint(function () {
                                            return ProposalService::maxWords(600);
                                        }),

                                    TextArea::make('research_questions')
                                        ->label(__(self::getFieldLabels('research_questions')))
                                        ->maxLength(16777215)
                                        ->autosize()
                                        ->required(!$isDraft)
                                        ->hintIcon('heroicon-o-question-mark-circle')
                                        ->hintIconTooltip(__('Brief descriptions of the research questions, focus, aims or issues that this project is intended to answer or address')),

                                    TextArea::make('previous_analysis')
                                        ->label(__(self::getFieldLabels('previous_analysis')))
                                        ->maxLength(16777215)
                                        ->autosize()
                                        ->required(!$isDraft)
                                        ->rules(
                                            ProposalService::maxWordsRule(300)
                                        )
                                        ->hint(function () {
                                            return ProposalService::maxWords(300);
                                        }),

                                    TextArea::make('expected_achievements')
                                        ->label(__(self::getFieldLabels('expected_achievements')))
                                        ->maxLength(16777215)
                                        ->autosize()
                                        ->required(!$isDraft)
                                        ->rules(
                                            ProposalService::maxWordsRule(400)
                                        )->validationMessages(['Max 400 words allowed'])
                                        ->hint(function () {
                                            return ProposalService::maxWords(400);
                                        }),

                                    TextArea::make('project_impacts')
                                        ->label(__(self::getFieldLabels('project_impacts')))
                                        ->maxLength(16777215)
                                        ->autosize()
                                        ->required(!$isDraft)
                                        ->rules(
                                            ProposalService::maxWordsRule(400)
                                        )
                                        ->hint(function () {
                                            return ProposalService::maxWords(400);
                                        })
                                        ->hintIcon('heroicon-o-question-mark-circle')
                                        ->hintIconTooltip(__('A list of expected impacts and achievements of the project')),

                                    TextArea::make('data_management_plan')
                                        ->label(__(self::getFieldLabels('data_management_plan')))
                                        ->maxLength(16777215)
                                        ->autosize()
                                        ->required(!$isDraft)
                                        ->rules(
                                            ProposalService::maxWordsRule(300)
                                        )
                                        ->hint(function () {
                                            return ProposalService::maxWords(300);
                                        })
                                        ->hintIcon('heroicon-o-question-mark-circle')
                                        ->hintIconTooltip(__('An open description of the data management procedures followed with the project, including licence issues, preferred data repositories, publication embargoes, common file formats, software and systems used, etc')),
                                    TextArea::make('references')
                                        ->label(__(self::getFieldLabels('references')))
                                        ->required(!$isDraft)
                                        ->helperText('min 5 - max 10')
                                        ->maxLength(16777215)
                                        ->autosize(),

                                    Textarea::make('comment')
                                        ->label('Comment')
                                        ->maxLength(65535)
                                        ->autosize()
                                        ->required(!$isDraft)
                                        ->helperText('Please specify the scientific motivation for continuing the work')
                                        ->hidden(fn(\Filament\Forms\Get $get): bool => $get('type') != ProposalType::LONG_TERM_PROJECT->name),
                                    TagsService::tagsGrid(
                                        'research_disciplines',
                                        'research_disciplines',
                                        'Research Disciplines',
                                        required: !$isDraft,
                                        multiple: true,
                                        searchable: true,
                                        helperText: 'This field will be used to assign the Peer Review Panellists to your application. Please choose carefully the most appropriate'
                                    ),
                                    Repeater::make('attachments')
                                        ->relationship()
                                        ->columns(2)
                                        ->schema([
                                            FileUpload::make('file_path')
                                                ->downloadable()
                                                ->previewable(false) // to show the actual file name in the form
                                                ->storeFileNamesIn('original_file_name'),
                                            TextArea::make('caption')
                                                ->label(__(self::getFieldLabels('caption')))
                                                ->autosize(),
                                        ])
                                        ->hintIcon('heroicon-o-question-mark-circle')
                                        ->hintIconTooltip(__('Add any image or document useful for describing the project')),

                                ]),


                        ]),

                    Step::make('Partners')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Textarea::make('cv')
                                ->label('Short Curriculum vitae of the User Group Leader')
                                ->autosize()
                                ->maxLength(16777215)
                                ->disabled()
                                ->dehydrated(true)
                                ->rules([
                                    function () {
                                        return function (string $attribute, $value, Closure $fail) {
                                            if (count(explode(' ', $value)) > 300) {
                                                $fail("Max 300 words for the {$attribute} allowed.");
                                            }
                                            return true;
                                        };
                                    },
                                ])
                                ->hint(function ($state, $component) {
                                    return 'Max 300 words, words left: ' . 300 - count(array_filter(explode(' ', trim($state))));
                                })
                                ->live(),
                            Repeater::make('applicantProposals')
                                ->label(function ($get) {
                                    $text = 'User Group participants involved in this proposal: ';

                                    $count = count($get('applicantProposals') ?? []);
                                    return new HtmlString($text . '(<b>' . $count . '</b>)');
                                })
                                ->validationAttribute('Partners')
                                ->addActionLabel('Add team member')
                                ->helperText(new HtmlString('Only users with a complete profile can be added as partners, if you don\'t see a user, please contact them and have them complete their profile. <br/>All the partners will be contacted via e-mail'))
                                ->relationship()

                                ->columnSpan('full')
                                ->schema([
                                    Select::make('applicant_id')
                                        ->label('Partner')
                                        ->required()
                                        ->searchable()
                                        ->options(
                                            User::role(User::USER_ROLE)
                                                ->where('complete_profile', 1)
                                                ->orderBy('surname')
                                                ->orderBy('name')
                                                ->get(['id', 'name', 'surname', 'email'])
                                                ->pluck('full_name_email', 'id')
                                        )
                                        ->optionsLimit(5)
                                        // ->afterStateUpdated(function (\Filament\Forms\Set $set, $state) {
                                        //     $user = User::find($state); //state contiene l'id dello user selezionato
                                        //     $set('email', $user->email ?? '');
                                        // })
                                        ->distinct()
                                        ->disabled(function (Get $get) {
                                            return $get('leader');
                                        })
                                        ->dehydrated(true)
                                        ->dehydratedWhenHidden()
                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                        ->live(),

                                    // Placeholder::make('email')
                                    //     ->disabled(function (Get $get) {
                                    //         return $get('leader');
                                    //     })
                                    //     ->dehydrated(false)
                                    //     ->content(fn(Get $get): string => User::find($get('applicant_id'))->email ?? ''),

                                    Forms\Components\Radio::make('leader')
                                        ->label('User group leader')
                                        ->inline()
                                        ->disabled()
                                        ->hidden(function (Get $get) {
                                            return !$get('leader');
                                        })
                                        ->dehydratedWhenHidden()
                                        ->dehydrated(true)
                                        ->default(false)
                                        ->inlineLabel(false)
                                        ->boolean(),
                                    Forms\Components\Radio::make('alias')
                                        ->label('Deputy')
                                        ->inline()
                                        ->disabled(function (Get $get) {
                                            return $get('leader');
                                        })
                                        ->hidden(function (Get $get) {
                                            return $get('leader');
                                        })
                                        ->dehydratedWhenHidden()
                                        ->dehydrated(true)
                                        ->required()
                                        ->inlineLabel(false)
                                        ->boolean()

                                ])
                                ->deleteAction(
                                    fn(Action $action) => $action
                                        ->requiresConfirmation(function (array $arguments, Repeater $component): bool {
                                            $items = $component->getState();
                                            $activeItem = $items[$arguments['item']];
                                            return !($activeItem['leader']);
                                        })
                                        ->action(function (array $arguments, Repeater $component): void {
                                            $items = $component->getState();
                                            $activeItem = $items[$arguments['item']];

                                            if ($activeItem['leader']) {
                                                Notification::make()
                                                    ->danger()
                                                    ->title('Error')
                                                    ->body('You cannot remove the leader partner')
                                                    ->send();
                                            } else {
                                                unset($items[$arguments['item']]);
                                                $component->state($items);
                                            }
                                        }),
                                )
                                ->columns(3),

                        ]),

                    Step::make('Additional information')
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            Radio::make('eu_or_national_projects_related')
                                ->boolean()
                                ->label(__(self::getFieldLabels('eu_or_national_projects_related')))
                                ->inline()
                                ->live()
                                ->inlineLabel(false),

                            TextInput::make('name_of_the_project')
                                ->label(__(self::getFieldLabels('name_of_the_project')))
                                ->maxLength(65535)
                                ->required(function (Get $get) use ($isDraft) {
                                    return $get('eu_or_national_projects_related') && !$isDraft;
                                })
                                ->hidden(function (Get $get) {
                                    return !$get('eu_or_national_projects_related');
                                }),

                            TextInput::make('founded_by')
                                ->label(__(self::getFieldLabels('founded_by')))
                                ->maxLength(65535)
                                ->required(function (Get $get) use ($isDraft) {
                                    return $get('eu_or_national_projects_related') && !$isDraft;
                                })
                                ->hidden(function (Get $get) {
                                    return !$get('eu_or_national_projects_related');
                                }),

                            TextInput::make('number_of_grant_agreement')
                                ->label(__(self::getFieldLabels('number_of_grant_agreement')))
                                ->maxLength(65535)
                                ->hidden(function (Get $get) {
                                    return !$get('eu_or_national_projects_related');
                                }),

                            Radio::make('training_activity')
                                ->boolean()
                                ->live()
                                ->label(__(self::getFieldLabels('training_activity')))
                                ->inline()
                                ->inlineLabel(false),

                            TextInput::make('training_activity_details')
                                ->label(__(self::getFieldLabels('training_activity_details')))
                                ->maxLength(65535)
                                ->required(function (Get $get) use ($isDraft) {
                                    return $get('training_activity') && !$isDraft;
                                })
                                ->hidden(function (Get $get) {
                                    return !$get('training_activity');
                                }),

                            CheckboxList::make('social_challenges')
                                ->label(__(self::getFieldLabels('social_challenges')))
                                ->options(ProposalSocialChallenges::options())
                                ->columns(),

                            Radio::make('industrial_involvement')
                                ->helperText(__('This information is used only for statistical purposes. Your answer will not affect your proposal'))
                                ->boolean()
                                ->live()
                                ->label(__(self::getFieldLabels('industrial_involvement')))
                                ->inline()
                                ->inlineLabel(false),

                            TextInput::make('industrial_involvement_details')
                                ->label(__(self::getFieldLabels('industrial_involvement_details')))
                                ->maxLength(65535)
                                ->required(function (Get $get) use ($isDraft) {
                                    return $get('industrial_involvement') && !$isDraft;
                                })
                                ->hidden(function (Get $get) {
                                    return !$get('industrial_involvement');
                                }),

                            Radio::make('learned_about_erihs')
                                ->label(__(self::getFieldLabels('learned_about_erihs')))
                                ->options(LearnedAboutErihs::options())
                                ->live()
                                ->inline()
                                ->inlineLabel(false),

                            TextInput::make('other_details')
                                ->label(__(self::getFieldLabels('other_details')))
                                ->maxLength(255)
                                ->required(function (Get $get) use ($isDraft) {
                                    return ($get('learned_about_erihs') == LearnedAboutErihs::OTHER->name) && !$isDraft;
                                })
                                ->hidden(function (Get $get) {
                                    return !($get('learned_about_erihs') == LearnedAboutErihs::OTHER->name);
                                }),
                        ]),
                    Step::make('Terms and conditions')
                        ->icon('heroicon-o-clipboard-document-check')
                        ->schema([
                            View::make('view_terms_and_conditions')
                                ->view('livewire.proposals.proposal_terms_and_conditions'),

                            Radio::make('terms_and_conditions')
                                ->helperText('The holder of the personal data processing is E-RIHS. You have
                                the right to obtain by the data holder, the
                                confirmation that data processing referred to you is taking place and, in such a case, you have the right to have
                                access to personal data, to correct or cancel them, or limit the processing; you have the right to refuse anytime
                                and also in the case of data processing for the scopes of direct marketing and of automated decision making.
                                Furthermore, you have the right of portability, of revoking your consent anytime, without prejudice to the
                                lawfulness of the processing based on consent before the revocation, and of proposing a complaint to a
                                supervisory authority. You can exercise your rights anytime, by writing an email to co@e-rihs.eu.')
                                ->label(__(self::getFieldLabels('terms_and_conditions')))
                                ->boolean()
                                ->live()
                                ->inline()
                                ->inlineLabel(false),

                            Checkbox::make('consent_to_videotape_and_photography')
                                ->label(__(self::getFieldLabels('consent_to_videotape_and_photography')))
                                ->inline()
                                ->inlineLabel(false),

                            Checkbox::make('news_via_email')
                                ->label(__(self::getFieldLabels('news_via_email')))
                                ->inline()
                                ->inlineLabel(false),
                        ])

                ])
                ->skippable()
                ->submitAction(view('proposal-submit-button', ['editingFromBackEnd' => $editingFromBackEnd]))
        ];
    }

    protected function onValidationError(ValidationException $exception): void
    {
        $this->dispatch('erihs:scroll-to', [
            'query' => '.filament-forms-field-wrapper-error-message',
        ]);
    }

    public function submit(): void
    {
        $this->isDraft = false;
        // this will ensure that the form is validated with the isDraft set to false
        $this->proposalForm = $this->getForms()['proposalForm'];
        $this->withValidator(function (Validator $validator) {
            $validator->after(function ($validator) {
                $this->errors = $validator->failed();
            });
        })->validate();
        $this->save(ProposalStatus::SUBMITTED->value);

        Redirect(route('proposal_success', ['proposal_id' => $this->proposal->id]));
    }

    public function saveAsDraft(): void
    {
        $tmp = $this->getForms()['proposalForm'];
        $this->isDraft = true;
        // this will ensure that the form is validated with the isDraft set to true
        $this->proposalForm = $this->getForms()['proposalForm'];
        $this->save(ProposalStatus::DRAFT->value);
        $this->isDraft = false;
        $this->proposalForm = $tmp;

        Notification::make('draft_saved')
            ->title('Draft saved successfully')
            ->success()
            ->send();

        $this->dispatch('refresh-form');
    }

    public function save($status)
    {
        $statusDB = $status;
        $state = $this->proposalForm->getState();

        if (isset($this->proposal->id)) {
            $proposal = $this->proposal;
            if (strcmp($status, ProposalStatus::DRAFT->value) === 0) {
                $proposal->withoutRevision()->update($state);
                $this->proposalForm->model($proposal)->saveRelationships();
            } else {
                $proposal->withoutRevision()->update($state);
                $this->proposalForm->model($proposal)->saveRelationships();
                if ($proposal->isInSecondDraft()) {
                    $proposal->makeTransitionAndSave(ProposalStatus::RESUBMITTED->value);
                } else {
                    $proposal->publish();
                    $proposal->makeTransitionAndSave($statusDB);
                    ERIHSCartService::emptyCart();
                }
            }
        } else {
            $proposal = Proposal::createDraft($state);
            $proposal->startFSMAndSave();
            $this->proposal = $proposal;
            if (strcmp($status, ProposalStatus::DRAFT->value) == 0) {
                $this->proposalForm->model($proposal)->saveRelationships();
            } else {
                $this->proposalForm->model($proposal)->saveRelationships();
                $proposal->publish();
                $proposal->makeTransitionAndSave($statusDB);
                ERIHSCartService::emptyCart();
            }
        }
    }

    protected function getFormModel(): string
    {
        return Proposal::class;
    }

    protected function getForms(): array
    {
        return [
            'proposalForm' => $this->makeForm()
                ->schema($this->getProposalFormSchema($this->proposal->id, $this->isDraft))
                ->statePath('data')
                ->model($this->proposal),
        ];
    }

    protected function getLayoutData(): array
    {
        return [
            'breadcrumbs' => [],
            'title' => __('Create a new proposal'),
            'maxContentWidth' => null,
        ];
    }

    public function render()
    {

        if (session()->has('was_creating_proposal')) {
            Notification::make()
                ->title('Thank you for completing your profile')
                ->body('You can now submit your proposals')
                ->success()
                ->send();
        }
        $view = view('livewire.create-proposal')->layout('components.layouts.app', $this->getLayoutData());

        return $view;
    }

    protected function addFormDataForTest(&$proposalFormData)
    {
        if (env('IS_TEST', false)) {
            $nProposals = DB::table('proposals')->count();
            $proposalFormData['proposalServices'][0]['number_of_days'] = 5;
            $proposalFormData['proposalServices'][0]['first_choice_start_date'] = "2024-01-01";
            $proposalFormData['proposalServices'][0]['first_choice_end_date'] = "2024-01-03";
            $proposalFormData['proposalServices'][0]['second_choice_start_date'] = "2024-02-01";
            $proposalFormData['proposalServices'][0]['second_choice_end_date'] = "2024-02-03";
            $proposalFormData['name'] = "P" . $nProposals . '-' . Str::random(8);
            $proposalFormData['acronym'] = "P" . $nProposals;
            $proposalFormData['type'] = "NEW";

            $tagsIds = DB::table('taggables')->where('taggable_type', "App\\Models\\User")
                ->get()->pluck('tag_id', 'tag_id')->all();
            $disciplines = DB::table('tags')->where('type', 'research_disciplines')
                ->whereIn('id', $tagsIds)->limit(3)->get();

            foreach ($disciplines as $discipline) {
                $proposalFormData['research_disciplines'][] = $discipline->id;
                $proposalFormData['research_disciplines_tags'][] = $discipline->name;
            }

            $proposalFormData['providers_contacted'] = 0;
            $proposalFormData['facility_contacted'] = 0;
        }
    }
}
