<?php

namespace Kazuto\Enlog\Tests;

use Illuminate\Support\Facades\File;
use Kazuto\Enlog\EnlogServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        File::cleanDirectory(app()->storagePath('logs'));
    }

    protected function getPackageProviders($app): array
    {
        return [
            EnlogServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}
