<?php

namespace Kazuto\Enlog\Support;

use Illuminate\Support\Collection;
use Iterator;
use SplFileInfo;

class LogFile
{
    private string $filename;

    private string $path;

    private string $fullPath;

    private int $size;

    public function __construct(
        SplFileInfo $file
    ) {
        $this->filename = $file->getFilename();
        $this->path = $file->getPath();
        $this->fullPath = $file->getPathName();
        $this->size = $file->getSize();
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getFullPath(): string
    {
        return $this->fullPath;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getReadableSize(): string
    {
        return $this->formatBytes($this->size);
    }

    public function getContent(): ?Collection
    {
        $iterator = $this->read();

        $buffer = '';

        foreach ($iterator as $iteration) {
            $buffer .= $iteration.PHP_EOL;
        }

        return Parser::parse($buffer)->map(fn (array $record) => new LogRecord(...$record));
    }

    private function read(): Iterator
    {
        $handle = fopen($this->fullPath, 'r');

        while (! feof($handle)) {
            yield trim(fgets($handle));
        }

        fclose($handle);
    }

    private function formatBytes($bytes): string
    {
        $units = ['b', 'kb', 'mb', 'gb', 'tb'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2).' '.$units[$pow];
    }
}
