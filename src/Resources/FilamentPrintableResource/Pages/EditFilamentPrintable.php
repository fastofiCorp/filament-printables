<?php

namespace FastofiCorp\FilamentPrintables\Resources\FilamentPrintableResource\Pages;

use FastofiCorp\FilamentPrintables\Resources\FilamentPrintableResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFilamentPrintable extends EditRecord
{
    protected static string $resource = FilamentPrintableResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
