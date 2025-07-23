<?php

namespace App\Filament\Pages;

use App\Enums\Gender;
use App\Enums\InstStatusCode;
use App\Enums\Position;
use App\Services\RorService;
use App\Services\TagsService;
use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\HtmlString;

class ProfileCommons
{

    public static function myDetailsFieldsSchema($elementsBefore = [])
    {
        return array_merge($elementsBefore, [

            TagsService::tagsGrid('title', 'personal_title', 'Personal title', required: false, multiple: false, searchable: true),
            TextInput::make('name')
                ->required(),
            TextInput::make('surname')
                ->required(),
            TextInput::make('email')
                ->unique(ignoreRecord: true)
                ->email()
                ->required(),
            TextInput::make('city')
                ->required(),
            TextInput::make('country')
                ->required(),
            TagsService::tagsGrid('nationality', 'country', 'Nationality', required: true, multiple: false, searchable: false),
            TextInput::make('birth_year')
                ->numeric()
                ->length(4)
                ->minValue(1900)
                ->maxValue(Carbon::now()->format('Y'))
                ->required(),
            Select::make('gender')
                ->required()
                ->options(Gender::options()),
            Placeholder::make('internal_statistic_notes')
                ->columnSpanFull()
                ->label('')
                ->content(new HtmlString('<div class="text-gray-500">Gender, nationality and birth year are required for internal statistics and for EU reporting. Neither are used as part of the technical or scientific evaluation of applications</div>'))
        ]);
    }

    public static function myInstitutionalInformationSchema($elementsBefore = [])
    {
        $rorService = new  RorService();
        return array_merge($elementsBefore, [
            Select::make('home_institution')
                ->label(__('Home institution (HI)'))
                ->searchable()
                ->getSearchResultsUsing(function ($query, callable $set) use ($rorService) {
                    $data = $rorService->retrieveOrganizationsByName($query);
                    $set('organization_options_names', $data['names']);
                    $set('organization_options_acronyms', $data['acronyms']);
                    return $data['names'];
                })->afterStateUpdated(
                    function ($state, callable $set, callable $get) {
                        $names = $get('organization_options_names');
                        // Imposta il valore di home_institution usando il nome corrispondente all'ID selezionato
                        $set('home_institution', $names[$state] ?? null);
                        // Imposta il valore di home_institution_id usando il valore selezionato
                        $set('home_institution_id', $state);
                    }
                )
                ->createOptionForm([
                    TextInput::make('name')
                        ->required(),
                ])
                ->createOptionUsing(function (array $data, $set) {

                    $set('organization_options_names', [$data['name'] => $data['name']]);
                    // Imposta il valore di home_institution usando il nome corrispondente all'ID selezionato
                    $set('home_institution', $data['name']);
                    // Imposta il valore di home_institution_id usando il valore selezionato
                    $set('home_institution_id', $data['name']);

                    return  $data['name'];
                })
                ->live(debounce: 800)
                ->extraAttributes(['onchange' => 'this.dispatchEvent(new Event("input"))'])
                ->required(),
            Hidden::make('home_institution_id')->required(),
            Select::make('institution_status_code')
                ->label(__('HI Legal Status Code'))
                ->required()
                ->options(InstStatusCode::options()),
            TagsService::tagsGrid('institution_country', 'institution_country', 'HI Country Code', required: true, multiple: false, searchable: false),

            TextInput::make('institution_address')
                ->label(__('Institution address'))
                ->required(),
            TextInput::make('institution_city')
                ->label(__('Institution city'))
                ->required(),
            TextInput::make('job')
                ->label(__('Function / Job / Title'))
                ->helperText('Describe your current job position')
                ->required(),
            Select::make('position')
                ->required()
                ->options(Position::options()),
            // TextInput::make('mailing_address')
            //     ->label(__('Mailing address'))
            //     ->required(),
            TextInput::make('office_phone')
                ->label(__('Phone (Office)'))
                ->tel()
                ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/'),
            Textarea::make('academic_background')
                ->label(__('Academic Background'))
                ->helperText('i.e. Chemistry; Physics; Archaeology; Conservation etc.')
                ->required()
                ->columnspan('full'),
        ]);
    }

    public static function myAccountSettingsSchema($elementsBefore = [])
    {
        return array_merge($elementsBefore, [
            Textarea::make('short_cv')
                ->required()
                ->label(__('Short Curriculum Vitae'))
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
                ->live(debounce: 500)

            // FileUpload::make('Your Curriculum Vitae')
            // ->acceptedFileTypes(['application/pdf', 'application/msword', 'image/*'])
        ]);
    }

    public static function myReviewerDetails($elementsBefore = [])
    {
        return [
            ...$elementsBefore,
            ...ReviewerAdditionalFields::formSchema(true)
        ];
    }
}
