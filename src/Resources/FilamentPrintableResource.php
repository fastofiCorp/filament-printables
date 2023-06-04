<?php

namespace FastofiCorp\FilamentPrintables\Resources;

use Closure;
use FastofiCorp\FilamentPrintables\Models\FilamentPrintable;
use FastofiCorp\FilamentPrintables\Resources\FilamentPrintableResource\Pages;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\Str;

class FilamentPrintableResource extends Resource
{
    protected static ?string $model = FilamentPrintable::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-report';

    public static function getModelLabel(): string
    {
        return __('filament-printables::filament-printables.resource.label.template');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-printables::filament-printables.resource.label.templates');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament-printables::filament-printables.resource.section.view'))
                    ->schema([
                        Forms\Components\MarkdownEditor::make('template_view')
                            ->columnSpan('full'),
                    ])
                    ->columnSpan(['lg' => 2]),
                Forms\Components\Section::make(__('filament-printables::filament-printables.resource.section.settings'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('filament-printables::filament-printables.resource.fields.name.label'))
                            ->reactive()
                            ->lazy()
                            ->afterStateUpdated(function (Closure $set, $state) {
                                $set('slug', Str::slug($state));
                            }),
                        Forms\Components\TextInput::make('slug')
                            ->label(__('filament-printables::filament-printables.resource.fields.slug.label'))
                            ->disabled()
                            ->required()
                            ->unique(FilamentPrintable::class, 'slug', ignoreRecord: true),
                        Forms\Components\Select::make('type')
                            ->required()
                            ->label(__('filament-printables::filament-printables.resource.fields.type.label'))
                            ->options([
                                'report' => __('filament-printables::filament-printables.resource.fields.type.options.report'),
                                'form' => __('filament-printables::filament-printables.resource.fields.type.options.form'),
                                'label' => __('filament-printables::filament-printables.resource.fields.type.options.label'),
                            ]),
                        Forms\Components\Select::make('format')
                            ->required()
                            ->label(__('filament-printables::filament-printables.resource.fields.format.label'))
                            ->options([
                                'pdf' => 'PDF',
                                'xlsx' => 'XLSX',
                            ])
                            ->multiple(),
                        Forms\Components\Select::make('linked_resources')
                            ->multiple()
                            ->required()
                            ->options(function () {
                                $subjects = [];
                                $exceptResources = [...config('filament-printables.resources.exclude'), self::class];
                                $removedExcludedResources = collect(Filament::getResources())->filter(function ($resource) use ($exceptResources) {
                                    return ! in_array($resource, $exceptResources);
                                });

                                foreach ($removedExcludedResources as $resource) {
                                    $model = $resource::getModel();
                                    $subjects[$model] = Str::of(class_basename($model))->headline();
                                }

                                return $subjects;
                            })
                            ->label(__('filament-printables::filament-printables.resource.fields.linked_resources.label')),

                    ])
                    ->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament-printables::filament-printables.resource.fields.name.label'))
                    ->sortable()
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label(__('filament-printables::filament-printables.resource.fields.slug.label'))
                    ->sortable()
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('filament-printables::filament-printables.resource.fields.type.label'))
                    ->sortable()
                    ->enum([
                        'report' => __('filament-printables::filament-printables.resource.fields.type.options.report'),
                        'form' => __('filament-printables::filament-printables.resource.fields.type.options.form'),
                        'label' => __('filament-printables::filament-printables.resource.fields.type.options.label'),
                    ])
                    ->searchable()
                    ->wrap(),

            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFilamentPrintables::route('/'),
            'create' => Pages\CreateFilamentPrintable::route('/create'),
            'edit' => Pages\EditFilamentPrintable::route('/{record}/edit'),
        ];
    }
}
