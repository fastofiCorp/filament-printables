<?php

namespace FastofiCorp\FilamentPrintables\Tests;

use FastofiCorp\FilamentPrintables\FilamentPrintablesServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            FilamentPrintablesServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        $migration = include __DIR__.'/../database/migrations/create_printables_table.php.stub';
        $migration->up();
    }
}
