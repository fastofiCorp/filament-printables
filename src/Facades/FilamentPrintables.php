<?php

namespace FastofiCorp\FilamentPrintables\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \FastofiCorp\FilamentPrintables\FilamentPrintables
 */
class FilamentPrintables extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \FastofiCorp\FilamentPrintables\FilamentPrintables::class;
    }
}
