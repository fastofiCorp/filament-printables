<?php

namespace FastofiCorp\FilamentPrintables\Resources\FilamentPrintableResource\Pages;

use FastofiCorp\FilamentPrintables\Resources\FilamentPrintableResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFilamentPrintable extends CreateRecord
{
    protected static string $resource = FilamentPrintableResource::class;
}
