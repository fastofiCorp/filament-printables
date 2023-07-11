<?php

namespace FastofiCorp\FilamentPrintables\Commands;

use Illuminate\Console\Command;

class filament-printablesCommand extends Command
{
    public $signature = 'filament-printables';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
