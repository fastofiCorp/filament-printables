<?php

namespace FastofiCorp\FilamentPrintables\Resources\FilamentPrintableResource\Pages;

use FastofiCorp\FilamentPrintables\Resources\FilamentPrintableResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFilamentPrintables extends ListRecords
{
    protected static string $resource = FilamentPrintableResource::class;

    protected function getTableEmptyStateHeading(): ?string
    {
        return __('filament-printables::filament-printables.resource.tables.empty-state');
    }


    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
