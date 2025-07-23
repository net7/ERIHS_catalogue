<?php

namespace App\Services;

use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Support\RawJs;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Tags\Tag;


use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TagsService
{

    public static function getOptions($type, $alphabetical)
    {
        $tags = Tag::getWithType($type);
        if ($alphabetical) {
            $tags = $tags->sortBy('name');
        }
        $tags = $tags->pluck('name', 'id');
        return $tags;
    }

    public static function tagsSchema($name, $type, $label, bool $required = false, bool $multiple = true, bool $searchable = false, bool $alphabetical = true, bool $addable = false)
    {
        $tagName = $name . '_tags';
        $select = Select::make($name)
            ->label($label)
            ->placeholder('Start typing to select an item')
            ->multiple($multiple)
            ->preload()
            ->searchable($searchable)
            ->afterStateUpdated(function (\Filament\Forms\Set $set, $state) use ($tagName, $multiple) {
                $tagsNames = [];
                if ($multiple) {
                    foreach ($state as $tagId) {
                        $tagsNames[] = Tag::find($tagId)->name;
                    }
                } else {
                    if (isset($state)) {
                        $tagsNames[] = Tag::find($state)->name;
                    }
                }
                $set($tagName, $tagsNames);
            })
            ->relationship(name: 'tags', titleAttribute: 'name', modifyQueryUsing: function (Builder $query) use ($type) {
                return $query->where('type', $type);
            })
            ->getSearchResultsUsing(
                fn(string $search): array => Tag::withType($type)->containing($search)->pluck('name', 'id')->toArray()
            )
            ->saveRelationshipsUsing(fn() => true)
            ->options(fn() => self::getOptions($type, $alphabetical))
            ->dehydrated(false)
            ->required($required);

        if ($addable) {
            $select = $select
                ->createOptionForm([
                    TextInput::make('name')
                        ->required(),
                ])
                ->createOptionModalHeading('Create ' . $label)
                ->createOptionUsing(function (array $data) use ($type) {
                    $tag = Tag::findOrCreateFromString($data['name'], $type);
                    // $set($name, $data['name']);
                    return $tag->id;
                })

            ;
        }

        $fieldSet = Fieldset::make('')
            ->schema([
                SpatieTagsInput::make($tagName)
                    ->type($type)
                    ->label('')
                    ->id('tagsinput-' . $tagName)
                    ->required($required)
            ])
            ->extraAttributes(['style' => 'display:none !important; visibility: hidden !important'])
        ;

        return [$select, $fieldSet];
    }


    public static function tagsGrid($name, $type, $label, bool $required = false, bool $multiple = true, bool $searchable = false, $hintIcon = null, $hintTooltip = null, $helperText = null, bool $alphabetical = true, bool $addable = false)
    {
        $grid = Grid::make(1)->schema(
            self::tagsSchema(
                name: $name,
                type: $type,
                label: $label,
                required: $required,
                multiple: $multiple,
                searchable: $searchable,
                alphabetical: $alphabetical,
                addable: $addable
            ),
        )->id('tagsgrid-' . $name)->columns(1)->columnSpan(1);

        if ($hintIcon != null) {
            $children = $grid->getChildComponents();
            foreach ($children as $id => $child) {
                if (get_class($child) == Select::class) {
                    $child->hintIcon(icon: $hintIcon, tooltip: $hintTooltip);
                }
            }
            $grid->childComponents($children);
        }

        if ($helperText != null) {
            $children = $grid->getChildComponents();
            foreach ($children as $id => $child) {
                if (get_class($child) == Select::class) {
                    $child->helperText($helperText);
                }
            }
            $grid->childComponents($children);
        }
        return $grid;
    }

    public static function tagsSchemaForRepeater(
        $name,
        $type,
        $label,
        bool $required = false,
        bool $multiple = true,
        bool $searchable = false,
        bool $preload = true,
        $hintIcon = null,
        $hintTooltip = null,
        bool $alphabetical = true,
        bool $canCreate = true
    ) {
        $select =
            Select::make($name)
                ->label($label)
                ->multiple($multiple)
                ->preload($preload)
                ->searchable($searchable)
                ->options(function ($state) use ($type, $multiple, $preload, $alphabetical) {

                    if ($preload) {
                        return self::getOptions($type, $alphabetical);
                    }
                    // Load the preloaded data for this instance
                    $relatedModel = Tag::withType($type)->find($state);
                    if ($multiple) {
                        if (empty($relatedModel)) {
                            return [];
                        }
                        return $relatedModel->pluck('name', 'id')->toArray();
                    } else {
                        return $relatedModel ? [$relatedModel->id => $relatedModel->name] : [];
                    }
                })
                ->getSearchResultsUsing(
                    fn(string $search): array => Tag::withType($type)->containing($search)->pluck('name', 'id')->toArray()
                )
                ->required($required)
        ;

        if ($canCreate) {
            $select->createOptionUsing(function (array $data, $set) use ($type, $name) {
                $tag = Tag::findOrCreateFromString($data['name'], $type);
                // $set($name, $data['name']);
                return $tag->id;
            })
                ->createOptionForm([
                    TextInput::make('name')
                        ->required(),
                ]);

        }

        if ($hintIcon && $hintTooltip) {
            $select->hintIcon($hintIcon, $hintTooltip);
        }
        return $select;
    }

    public static function getTaggables(Tag $tag): Collection
    {
        $taggables = DB::table('taggables')
            ->where('tag_id', $tag->id)
            ->get();

        $ret = [];
        foreach ($taggables as $taggable) {
            $taggable_type = $taggable->taggable_type;
            $ret[$taggable_type][] = $taggable->taggable_id;
        }
        return collect($ret);
    }

    public static function getUsageInRepeater(Tag $tag): Collection
    {
        $usages = [];

        // Method
        foreach (DB::table('methods')
            ->whereJsonContains('method_parameter', [['parameter_type_tag_field' => "$tag->id"]])
            ->orWhereJsonContains('method_parameter', [['parameter_unit_tag_field' => "$tag->id"]])
            ->get() as $model) {
            $usages['App\Models\Method'][] = $model->id;
        }

        // Organization
        foreach (DB::table('organizations')
            ->whereJsonContains('research_references', [['reference_role_tag_field' => "$tag->id"]])
            ->orWhereJsonContains('external_pid', [['pid_type_tag_field' => "$tag->id"]])
            ->get() as $model) {
                $usages['App\Models\Organization'][] = $model->id;
        }

        // Service
        foreach (DB::table('services')
            ->whereJsonContains('measurable_properties', [['class_tag_field' =>  "$tag->id"]])
            ->orWhereJsonContains('measurable_properties', [['materials_tag_field' => "$tag->id"]])
            ->orWhereJsonContains('links', [['type_tag_field' => "$tag->id"]])
            ->get() as $model) {
                $usages['App\Models\Service'][] = $model->id;
        }

        // Tool
        foreach (DB::table('tools')
            ->whereJsonContains('url', [['link_type_tag_field' => "$tag->id"]])
            ->get() as $model) {
                $usages['App\Models\Tool'][] = $model->id;
            }

        return collect($usages);

    }

    public static function getUsage(Tag $tag): Collection
    {
        return self::getUsageInRepeater($tag)->merge(self::getTaggables($tag));
    }



    public static function isUsedInSomeRepeater(Tag $tag): bool
    {
        return self::getUsageInRepeater($tag)->count() > 0;
    }

    public static function isUsed(Tag $tag)
    {
        return self::getUsage($tag)->count() > 0 ;
    }
}
