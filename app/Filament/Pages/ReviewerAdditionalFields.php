<?php

namespace App\Filament\Pages;

use App\Services\ProposalService;
use App\Services\TagsService;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Get;
use Filament\Pages\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Spatie\Tags\Tag;

class ReviewerAdditionalFields extends Page
{

    protected static string $view = 'filament.pages.reviewer-additional-fields';
    public array $formData;
    protected static bool $shouldRegisterNavigation = false;
    public $user;
    public ?array $data = [];

    public function getHeading(): string
    {
        return '';
    }

    public function getFormStatePath(): ?string
    {
        return 'data';
    }

    public function mount(): void
    {
        $this->user = Auth::user();
        $userTags = $this->user->tags()->allRelatedIds();
        $researchDisciplines = [];
        $techniques = [];
        $materials = [];
        foreach ($userTags as $userTag) {
            $tag = Tag::find($userTag);
            if ($tag->type == 'research_disciplines') {
                $researchDisciplines[] = $userTag;
            }
            if ($tag->type == 'technique') {
                $techniques[] = $userTag;
            }
            if ($tag->type == 'material') {
                $materials[] = $userTag;
            }
        }
        $this->form->fill([
            'number_of_reviews' => $this->user->number_of_reviews ?? env('NUMBER_OF_REVIEWS_PER_YEAR'),
            'object_types' => $this->user->object_types,
            'research_disciplines' => $researchDisciplines,
            'techniques' => $techniques,
            'materials' => $materials,
            'terms_of_service' => $this->user->terms_of_service,
            // 'confidentiality' => $this->user->confidentiality,
        ]);

    }

    public function getFormSchema(): array
    {
        return [
            Section::make(__('You have been assigned the role of reviewer. You must accept the terms and conditions of use before proceeding.'))->schema([
                ...self::formSchema(),
                ViewField::make('submit')
                    ->view('submit')

            ])];
    }

    public static function formSchema($isEdit = false)
    {
        return [
            TagsService::tagsGrid('research_disciplines', 'research_disciplines', 'Research Disciplines', true, true, true),
            TagsService::tagsGrid(
                'techniques',
                'technique',
                'Technique',
                required: true,
                multiple: true,
                searchable: true
            ),
            TagsService::tagsGrid(
                'materials',
                'material',
                'Material',
                required: true,
                multiple: true,
                searchable: true,
            ),
            TagsInput::make('object_types')
                ->required()
                ->label(__('Object types'))
                ->suggestions(
                    ProposalService::getAllObjectTypes()
                ),
            TextInput::make('number_of_reviews')
                ->hint(__('Specify how many reviews you can do in a year'))
                ->label(__('Reviews'))
                ->integer()
                ->minValue(3)
                ->required(),

            CheckBox::make('terms_of_service')
                ->label(fn(Get $get): HtmlString => new HtmlString(__('I accept the ' .
                    '<a target="_blank" href="' . route('terms.showReviewerTerms') . '" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">' . __('Terms of Reference') . '</a>')))
                ->hidden(fn(Get $get): bool => $get('terms_of_service') === true)
                ->dehydratedWhenHidden(true)
                ->accepted()
                ->required()
                ->validationMessages([
                    'accepted' => 'You must accept the Terms of Reference to continue.',
                ]),


            \Filament\Forms\Components\View::make('terms_status')
                ->visible(fn(Get $get): bool => $get('terms_of_service') === true)
                ->columnSpan('full'),

        ];
    }

    public function render(): View
    {
        return view('filament.pages.reviewer-additional-fields');
    }

    public function submit()
    {
        $state = $this->form->getRawState();
        $researchDisciplines = $state['research_disciplines'];
        $techniques = $state['techniques'];
        $materials = $state['materials'];
        $this->user->terms_of_service = $state['terms_of_service'];
        $this->user->confidentiality = $state['terms_of_service'];
        // $this->user->confidentiality = $state['confidentiality'];
        $tagsToDetach = $this->user->tags()->allRelatedIds();
        foreach ($tagsToDetach as $oldTag) {
            $tag = Tag::find($oldTag);
            $this->user->detachTag($tag);
        }
        foreach ($researchDisciplines as $discipline) {
            $tag = Tag::find($discipline);
            $this->user->attachTag($tag);
        }

        foreach ($techniques as $technique) {
            $tag = Tag::find($technique);
            $this->user->attachTag($tag);
        }

        foreach ($materials as $material) {
            $tag = Tag::find($material);
            $this->user->attachTag($tag);
        }

        $this->user->object_types = $state['object_types'];
        $this->user->number_of_reviews = $state['number_of_reviews'];
        $this->user->update();
        $route = auth()->user()->first_login ? 'wizard' : 'dashboard';
        return $state['number_of_reviews'] >= env('NUMBER_OF_REVIEWS_PER_YEAR') ? redirect()->to(route($route)) : '#';
    }
}
