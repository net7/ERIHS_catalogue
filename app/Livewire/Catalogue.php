<?php

namespace App\Livewire;

use App\Models\Service;
use Elastic\ScoutDriverPlus\Support\Query;
use Exception;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Catalogue extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;
    use WithPagination;

    #[Url(as: 'q', history: true, except: '')]
    public $searchedText;

    private $searchResult;

    private $aggregations;

    public $resultCount;

    #[Url(as: 'p', history: true, except: '')]
    public $platforms = [];

    #[Url(as: 'c', history: true, except: '')]
    public $countries = [];

    #[Url(as: 't', history: true, except: '')]
    public $techniques = [];

    #[Url(as: 'o', history: true, except: '')]
    public $organizations = [];

    #[Url(as: 'm', history: true, except: '')]
    public $materials = [];

    #[Url(as: 'r', history: true, except: '')]
    public $researchDisciplines = [];

    public $elasticDown = false;

    private $services;


    public int|string $itemsPerPage = 10;

    protected function getForms(): array
    {
        return [
            'filterForm',
            'searchForm'
        ];
    }

    public function searchForm(Form $form): Form
    {
        return
            $form->schema([
                Section::make()->columnSpan(1)->schema([
                    Placeholder::make('')
                        ->content(new HtmlString(
                            '<div class="py-3 justify-start items-center inline-flex">
                        <div class="grow shrink basis-0 flex-col justify-start items-start inline-flex">
                        <div class="text-gray-900 text-xl font-semibold font-[\'Montserrat\'] leading-7">Search</div>
                        <div class="text-gray-500 text-sm font-normal font-[\'Montserrat\'] leading-tight">Find the services you need quickly and easily</div>
                        </div>
                        </div>'
                        ))
                        ->columnSpan('full')
                        ->extraAttributes([
                            'class' => "text-gray-900 text-xl font-semibold leading-7 font-['Montserrat']",
                        ]),
                    Forms\Components\TextInput::make('searchedText')
                        ->label('')
                        ->live(debounce:1000)
                        ->placeholder('Search')
                        ->suffixAction(fn(): Action => Action::make('submit')
                            ->icon('heroicon-o-magnifying-glass')
                            ->action(fn() => $this->submit())
                        ),
                ]),

            ]);
    }

    public function filterForm(Form $form): Form
    {
        $this->loadAggregations();
        $platforms = $this->getPlatformAggregations();
        $countries = $this->getCountriesAggregations();
        $techniques = $this->getTechniquesAggregations();
        $materials = $this->getMaterialsAggregations();
        $organizations = $this->getOrganizationsAggregations();
        $researchDisciplines = $this->getResearchDisciplinesAggregations();

        return $form->schema([
            Section::make()->columnSpan(1)->schema([
                Placeholder::make('')
                    ->content(new HtmlString(
                        '<div class="py-3 justify-start items-center inline-flex">
                                    <div class="grow shrink basis-0 flex-col justify-start items-start inline-flex">
                                        <div class="text-gray-900 text-xl font-semibold font-[\'Montserrat\'] leading-7">
                                            Filters
                                        </div>
                                    </div>
                                </div>'
                    ))
                    ->columnSpan('full')
                    ->extraAttributes([
                        'class' => "text-gray-900 text-xl font-semibold leading-7 font-['Montserrat']",
                    ]),

                Actions::make([
                     Action::make('resetFilters')
                         ->label('Reset filters')
                         ->action(fn() => $this->resetFilters()),
                 ])
                ->fullWidth(),

                CheckboxList::make('platforms')
                    ->label('Platforms')
                    ->options($platforms->pluck('label', 'id'))
                    ->descriptions($platforms->pluck('count', 'id'))
                    ->live(),

                Select::make('organizations')
                    ->label('Organizations')
                    ->multiple()
                    ->searchable()
                    ->options($organizations->pluck('label', 'label'))
                    ->live(),

                Select::make('countries')
                    ->label('Countries')
                    ->multiple()
                    ->searchable()
                    ->options(

                        $countries->pluck('label', 'label')


                        // function() use ($countries){
                        //     $options = [];
                        //     foreach ($countries as $country){
                        //         $options [$country['label']] = $country['label'] . ' (' . $country['count'] .')';
                        //     }
                        //     return $options;
                        // }
                    )
                    ->live(),
                Select::make('techniques')
                    ->label('Techniques')
                    ->multiple()
                    ->searchable()
                    ->options($techniques->pluck('label', 'label'))
                    ->live(),

                Select::make('materials')
                    ->label('Materials')
                    ->multiple()
                    ->searchable()
                    ->options($materials->pluck('label', 'label'))
                    ->live(),

                Select::make('researchDisciplines')
                    ->label('Fields of application')
                    ->multiple()
                    ->searchable()
                    ->options($researchDisciplines->pluck('label', 'label'))
                    ->live(),

            ]),
        ]);
    }

    public function loadAggregations()
    {
        $query = $this->constructQuery(excludeFilters: true);
        $aggResult = Service::searchQuery($query)
            ->aggregate('countries', [
                'terms' => [
                    'field' => 'organization.country.keyword',
                    'size' => 9999,
                    'order' => ['_key' => 'asc'],
                ],
            ])
            ->aggregate('organizations', [
                'terms' => [
                    'field' => 'organization.name.keyword',
                    'size' => 9999,
                    'order' => ['_key' => 'asc'],
                ],
            ])
            ->aggregate('techniques', [
                'terms' => [
                    'field' => 'techniques.keyword',
                    'size' => 9999,
                    'order' => ['_key' => 'asc'],
                ],
            ])
            ->aggregate('materials', [
                'terms' => [
                    'field' => 'materials.keyword',
                    'size' => 9999,
                    'order' => ['_key' => 'asc'],
                ],
            ])
            ->aggregate('research_disciplines', [
                'terms' => [
                    'field' => 'research_disciplines.keyword',
                    'size' => 9999,
                    'order' => ['_key' => 'asc'],
                ],
            ])
            ->aggregate('platforms', [
                'terms' => [
                    'field' => 'platforms.keyword',
                    'min_doc_count' => 0,
                    'order' => ['_key' => 'asc'],
                ],
            ])
            ->size(0)
            ->execute();

        $this->aggregations = $aggResult->aggregations();
    }

    private function getFilterArrayFromBucket($bucket, $klass, $nameField = 'name')
    {
        $model = $klass::find($bucket['key']);

        return [
            'label' => $model->$nameField,
            'id' => $bucket['key'],
            'count' => $bucket['doc_count'],
        ];
    }


    private function getFilterArrayFromBucketTags($bucket)
    {
        return [
            'label' => $bucket['key'],
            'id' => $bucket['key'],
            'count' => $bucket['doc_count'],
        ];
    }

    private function getAggregationsFromSearchResult($aggregation, $klass)
    {
        $res = [];
        $aggregation = $this->aggregations->get($aggregation);
        foreach ($aggregation['buckets'] as $bucket) {
            $res[] = $this->getFilterArrayFromBucket($bucket, $klass);
        }

        return collect($res);
    }

    private function getAggregationsFromTagsSearchResult($aggregation)
    {
        $res = [];
        $aggregation = $this->aggregations->get($aggregation);
        foreach ($aggregation['buckets'] as $bucket) {
            $res[] = $this->getFilterArrayFromBucketTags($bucket);
        }

        return collect($res);
    }

    private function getCountriesAggregations()
    {
        return $this->getAggregationsFromTagsSearchResult('countries');
    }

    private function getTechniquesAggregations()
    {
        return $this->getAggregationsFromTagsSearchResult('techniques');
    }

    private function getMaterialsAggregations()
    {
        return $this->getAggregationsFromTagsSearchResult('materials');
    }

    private function getOrganizationsAggregations()
    {
        return $this->getAggregationsFromTagsSearchResult('organizations');
    }

    private function getResearchDisciplinesAggregations()
    {
        return $this->getAggregationsFromTagsSearchResult('research_disciplines');
    }


    private function getPlatformAggregations()
    {
        return $this->getAggregationsFromTagsSearchResult('platforms');
    }


    public function constructQuery($excludeFilters = false)
    {
        $text = $this->searchedText;
        if (!is_array($text)){
            $text = trim($text);
        }
        $query = Query::bool();


        $termQuery = Query::term()
            ->field('service_active')
            ->value(1);
        $query->must($termQuery);
        if (!$text) {
            $textQuery = Query::matchAll();
            $query->must($textQuery);

        } else {
            // $nameQuery = Query::wildcard()
            //     ->field('title')
            //     ->value($text . '*')
            //     ->caseInsensitive(true);

            // $decriptionQuery = Query::wildcard()
            //     ->field('description')
            //     ->value($text . '*')
            //     ->caseInsensitive(true);


            $nameQuery = Query::multiMatch()
            // ->fields(['title^500','description^300','summary^200','categories.category','functions.function','materials','research_disciplines', 'organization.name', 'techniques', 'other_techniques'])
            ->fields(['title','description','summary','categories.category','functions.function','materials','research_disciplines', 'organization.name', 'techniques', 'other_techniques'])
            ->query($text)
                ->operator('and');

            $query->must(Query::bool()
                ->should($nameQuery)
                // ->should($decriptionQuery)
            );
        }

        if ($this->platforms && !$excludeFilters) {
            $platformsFilterQuery = Query::terms()
                ->field('platforms.keyword')
                ->values($this->platforms);
            $query->filter($platformsFilterQuery);
        }

        if ($this->organizations) {
            $organizationsFilterQuery = Query::terms()
                ->field('organization.name.keyword')
                ->values($this->organizations);
            $query->filter($organizationsFilterQuery);
        }

        if ($this->techniques) {
            $techniquesFilterQuery = Query::terms()
                ->field('techniques.keyword')
                ->values($this->techniques);
            $query->filter($techniquesFilterQuery);
        }

        if ($this->researchDisciplines) {
            $researchDisciplinesFilterQuery = Query::terms()
                ->field('research_disciplines.keyword')
                ->values($this->researchDisciplines);
            $query->filter($researchDisciplinesFilterQuery);
        }

        if ($this->materials) {
            $materialsFilterQuery = Query::terms()
                ->field('materials.keyword')
                ->values($this->materials);
            $query->filter($materialsFilterQuery);
        }

        if ($this->countries) {
            $countriesFilterQuery = Query::terms()
                ->field('organization.country.keyword')
                ->values($this->countries);
            $query->filter($countriesFilterQuery);
        }

        return $query;

    }

    public function loadServices(): void
    {
        $query = $this->constructQuery();
        $this->searchResult = Service::searchQuery($query)
            ->sort('title.keyword', 'asc')
            ->paginate($this->itemsPerPage);
        $this->services = $this->searchResult->items();

        $this->resultCount = $this->searchResult->total();
    }


    public function resetFilters(): void
    {
        $this->countries = [];
        $this->platforms = [];
        $this->organizations = [];
        $this->techniques = [];
        $this->materials = [];
        $this->researchDisciplines = [];
        $this->filterForm->fill($this->platforms);
        $this->filterForm->fill($this->organizations);
        $this->filterForm->fill($this->countries);
        $this->filterForm->fill($this->techniques);
        $this->filterForm->fill($this->materials);
        $this->filterForm->fill($this->researchDisciplines);
        // $this->updated();
    }


    public function update()
    {
$a ='';
    }

    public function updated(): void
    {
        $this->resetPage();
    }

    public function submit(): void
    {
        $this->resetPage();
    }



    public function mount(): void
    {
        try{
            $this->searchedText = request()->input('q', []);
            $this->organizations = request()->input('o', []);
            $this->platforms = request()->input('p', []);
            $this->countries = request()->input('c', []);
            $this->techniques = request()->input('t', []);
            $this->materials = request()->input('m', []);
            $this->researchDisciplines = request()->input('r', []);
            $this->searchForm->fill([$this->searchedText]);
            $this->filterForm->fill($this->platforms);
            $this->filterForm->fill($this->organizations);
            $this->filterForm->fill($this->countries);
            $this->filterForm->fill($this->techniques);
            $this->filterForm->fill($this->materials);
            $this->filterForm->fill($this->researchDisciplines);
        } catch(\Exception $e){
            $this->elasticDown = true;
        }
    }

    public function render()
    {
        try {
            $this->loadServices();
        }   catch(Exception $e) {
            $this->elasticDown = true;
        }

        $this->dispatch('catalogue-scroll-to-top');

        return view('livewire.catalogue')->with(
            [
                'elasticDown' => $this->elasticDown,
                'services' => $this->services,
                'searchResult' => $this->searchResult,
            ]
        );
    }
}
