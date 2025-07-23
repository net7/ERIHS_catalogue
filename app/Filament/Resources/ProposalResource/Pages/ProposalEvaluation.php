<?php

namespace App\Filament\Resources\ProposalResource\Pages;

use App\Filament\Resources\ProposalResource;
use App\Models\Proposal;
use App\Models\ProposalEvaluation as ModelsProposalEvaluation;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Tables;
use Filament\Tables\Table;
use IbrahimBougaoua\FilamentRatingStar\Forms\Components\RatingStar;
use Illuminate\Support\Facades\Auth;
use AymanAlhattami\FilamentPageWithSidebar\FilamentPageSidebar;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Livewire\Component;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use App\Services\UserService;

class ProposalEvaluation extends EditRecord
{

    use HasPageSidebar;
    protected static string $resource = ProposalResource::class;


    protected static ?string $breadcrumb = "Evaluation";
    protected static ?string $title = "Evaluation";

    public ?bool $evaluationExists = null;

    public ?array  $data = [];

    protected function getRedirectUrl(): ?string
    {
        return 'evaluation';
    }

    public static function canAccess(array $parameters = []): bool
    {
        $user = Auth::user();

        return UserService::canUserEvaluateProposal($user, $parameters['record']);
    }

    public function getHeader(): ?\Illuminate\Contracts\View\View
    {

        return view('components.inline-js', [
            'script' => "
                document.addEventListener('livewire:init', () => {
                    Livewire.on('close-modal', (event) => {
                        const modal = document.querySelector('.fi-modal-window');
                        if (modal) {
                        // console.log('chiudo '  + modal);
                            modal.remove();
                            modal.close();
                            modal.dispatchEvent(new Event('close'));
                        }
                    });
                });
            ",
        ]);

    }

    protected function getSaveFormAction(): \Filament\Actions\Action
    {


            return \Filament\Actions\Action::make('save')
                ->label(label: __('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->requiresConfirmation()
                ->modalHeading('Confirm evaluation')
                ->modalDescription(new HtmlString('Are you sure you want to evaluate this proposal? <br/> Once you confirm, you will not be able to modify the evaluation!'))
                // ->modalSubmitActionLabel('Yes, evaluate it')
                ->action(fn () => $this->save())
                // ->submit('save')
                ->keyBindings(['mod+s'])
                ->hidden(function (): bool {
                    return $this->evaluationExists;
                });


    }
    protected function getCancelFormAction(): \Filament\Actions\Action
    {

        return parent::getCancelFormAction()
            ->hidden(function (): bool {
            return $this->evaluationExists;
        });

    }
    public function form(Form $form): Form
    {

        return $form
            ->schema([
                Section::make()->columns(1)->schema([

                    Section::make('Excellence')->schema([  
                        RatingStar::make('excellence_relevance')
                            ->label('Relevance of the research questions')
                            ->helperText('Are the research questions appropriate and significant? To what extent will the research enhance knowledge of an object/site, advance heritage science research or the wider multidisciplinary cultural heritage research field to which heritage science belongs?')
                            ->required(!$this->evaluationExists)
                            ->live(),
                        RatingStar::make('excellence_methodology')
                            ->label('Methodology and research plan')    
                            ->helperText('Are the research methods and plan appropriate and will they answer the research questions? Are they clearly described and robust?')
                            ->required(!$this->evaluationExists)
                            ->live(),
                        RatingStar::make('excellence_originality')
                            ->label('Originality')
                            ->helperText('Novelty and innovativeness of the proposed research. How original and new is the research?')
                            ->required(!$this->evaluationExists)
                            ->live(),   
                        RatingStar::make('excellence_expertise')
                            ->label('Expertise of user group')
                            ->helperText('Is the expertise of the entire user group relevant and sufficient to make a success of the research? Does the expertise of the user group include the multiple relevant disciplines?/sectors?')
                            ->required(!$this->evaluationExists)
                            ->live(),
                        RatingStar::make('excellence_timeliness')
                            ->label('Timeliness')
                            ->helperText('Relevance to current issues and advancements in heritage science. To what extent does it advance past research in the same areas?')
                            ->required(!$this->evaluationExists)
                            ->live(),
                        RatingStar::make('excellence_state_of_the_art')
                            ->label('State-of-the-art')
                            ->helperText('Complete, relevant and of quality.  Is the research background adequately described? Is key past research missing or not cited? How does the project build on past research?')
                            ->required(!$this->evaluationExists)
                            ->live(),
                    ]),

                    Section::make('Impact')->schema([
                        RatingStar::make('impact_research')
                            ->label('Research community impact')
                            ->helperText('Importance and significance of the issue and expected outcomes for the community specialized on/concerned by the topic.')
                            ->required(!$this->evaluationExists)
                            ->live(),
                        RatingStar::make('impact_knowledge_sharing')
                            ->label('Knowledge Sharing')
                            ->helperText('Plans for publishing results and disseminating findings to the broader community. Will the plans reach all the relevant interdisciplinary communities?')
                            ->required(!$this->evaluationExists)
                            ->live(),   
                        RatingStar::make('impact_innovation_potential')
                            ->label('Innovation Potential')
                            ->helperText('How innovative is the proposed research (e.g., high-risk high-gain exploratory project)? To what extent does it open new avenues in heritage science or multidisciplinary knowledge of heritage objects/sites?')
                            ->required(!$this->evaluationExists)
                            ->live(),   
                        RatingStar::make('impact_open_access')
                            ->label('Open Access')
                            ->helperText('Is there commitment and appropriate plans for managing, archiving and documenting the generated data and results to enhance accessibility and transparency for future re-use?')
                            ->required(!$this->evaluationExists)
                            ->live(),
                        RatingStar::make('impact_expected_impacts')
                            ->label('Expected Impacts on Society or Industry')
                            ->helperText('Anticipated positive societal and/or economic effects including public understanding of cultural heritage, education, and industry etc.')
                            ->required(!$this->evaluationExists)
                            ->live(),
                    ]), 

                    Textarea::make('comment')
                        ->required()
                        ->autosize()
                        ,
                    TextInput::make('reviewer_id')
                        ->default(Auth::user()->id)
                        ->label('')
                        ->extraAttributes(['class' => 'hidden'])

                ])->disabled($this->evaluationExists)
            ])
            ;
    }
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('scientific_excellence')
                    ->label('')
                    // ->description(__('Scientific Excellence'))
                    ->tooltip('Scientific Excellence'),
                Tables\Columns\TextColumn::make('state_of_the_art_topic'),
                Tables\Columns\TextColumn::make('valorization_and_dissemination_plan'),
                Tables\Columns\TextColumn::make('expertise_of_user_group'),
                Tables\Columns\TextColumn::make('potential_impact'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    protected function handleRecordUpdate(Model $proposal, array $data): Model
    {
        $reviewer_id = Auth::user()->id;
        $evaluation = ModelsProposalEvaluation::firstOrNew(['proposal_id' => $proposal->id, 'reviewer_id' => $reviewer_id]);

        foreach ($data as $key => $value) {
            $evaluation->{$key} = $value;
        }
        $evaluation->reviewer_id = Auth::user()->id;
        $evaluation->save();
        return $evaluation;
    }


    public function mount(int | string $record): void
    {

        $proposal = $this->resolveRecord($record);

        $reviewer_id = Auth::user()->id;

        $evaluation = ModelsProposalEvaluation::firstOrNew(['proposal_id' => $proposal->id, 'reviewer_id' => $reviewer_id]);
        // set the record to evaluation in order to fill the form
        $this->record = $evaluation;

        $this->evaluationExists = (
            $evaluation->excellence_relevance != null &&
            $evaluation->excellence_methodology != null &&
            $evaluation->excellence_originality != null &&
            $evaluation->excellence_expertise != null &&
            $evaluation->excellence_timeliness != null &&
            $evaluation->excellence_state_of_the_art != null &&
            $evaluation->impact_research != null &&
            $evaluation->impact_knowledge_sharing != null &&
            $evaluation->impact_innovation_potential != null &&
            $evaluation->impact_open_access != null &&
            $evaluation->impact_expected_impacts != null
        );
        $this->form->fill($evaluation->toArray());
        // set the record back to the proposal, so that the rest of the page works
        $this->record = $proposal;


        return;
    }


    public static function sidebar(Proposal $record): FilamentPageSidebar
    {
        return FilamentPageSidebar::make()
            ->setNavigationItems(static::navigationItems($record));
    }
}
