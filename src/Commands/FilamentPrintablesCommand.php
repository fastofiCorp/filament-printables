<?php

namespace FastofiCorp\FilamentPrintables\Commands;

use Illuminate\Console\Command;

class FilamentPrintablesCommand extends Command
{
    public $signature = 'FilamentPrintables';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
