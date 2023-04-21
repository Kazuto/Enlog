<?php

namespace Kazuto\Enlog\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ItemNotFoundException;
use SplFileInfo;

class Aggregator
{
    private string $path;

    private Collection $files;

    public function __construct(
        ?string $path = null
    ) {
        $this->path = $path ?? app()->storagePath('logs');

        $this->aggregate();
    }

    public function get(): Collection
    {
        $entries = [];

        $this->files
            ->each(function (SplFileInfo $fileInfo) use (&$entries) {
                [$key] = str($fileInfo->getFilename())->explode('.');

                if (str_contains($key, '-')) {
                    [$key] = str($key)->explode('-');
                }

                return $entries[$key][] = new LogFile($fileInfo);
            });

        return collect($entries);
    }

    /**
     * @throws ItemNotFoundException
     */
    public function find(string $filename): LogFile
    {
        $fileInfo = $this->files
            ->filter(fn (SplFileInfo $fileInfo) => $fileInfo->getFilename() === $filename)
            ->firstOrFail();

        return new LogFile($fileInfo);
    }

    public function sort(bool $desc = true): self
    {
        $this->files = $this->files
            ->sortBy(fn (SplFileInfo $fileInfo) => $fileInfo->getFilename(), descending: $desc);

        return $this;
    }

    private function aggregate(): void
    {
        $this->files = collect(File::files($this->path))
            ->filter(fn (SplFileInfo $fileInfo) => $this->isLog($fileInfo));
    }

    private function isLog(SplFileInfo $fileInfo): bool
    {
        return str($fileInfo->getFilename())->endsWith('.log');
    }
}
