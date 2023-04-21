<?php

namespace Kazuto\Enlog\Tests;

use Illuminate\Support\Facades\File;
use Kazuto\Enlog\EnlogServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use SplFileInfo;

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

    public function getTestFile(): SplFileInfo
    {
        return collect(File::files(app()->storagePath('logs')))
            ->filter(fn (SplFileInfo $fileInfo) => str($fileInfo->getFilename())->contains('laravel'))
            ->first();
    }
}
