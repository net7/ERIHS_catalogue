<?php

namespace App\Filament\Resources\ProposalResource\Pages;

use App\Filament\Resources\ProposalResource;
use App\Models\Call;
use App\Enums\ErihsPlatform;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Spatie\Tags\Tag;
class ListProposals extends ListRecords
{
    protected static string $resource = ProposalResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function table(Table $table): Table
    {
        $table = parent::table($table);

        $table->filters([
            SelectFilter::make('call_id')
                ->label('Call')
                ->options(Call::all()->sortByDesc('end_date')->pluck('label', 'id')),
            SelectFilter::make('getPlatforms')
                ->label('Platforms')
                ->options(function () {
                    return Tag::where('type', 'e-rihs_platform')->get()->pluck('name', 'name');
                })
                ->query(function ($query, array $data) {
                    if (! isset($data['value'])) return;
            
                    $query->whereHas('services', function ($q) use ($data) {
                        $q->whereHas('platformTags', function ($q2) use ($data) {
                            $q2->where('name->en',$data['value']);
                        });
                    });
                })
        ]);
        return $table;
    }
}
