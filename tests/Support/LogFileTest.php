<?php

use Illuminate\Support\Collection;
use Kazuto\Enlog\Support\LogFile;
use Kazuto\Enlog\Support\LogRecord;

it('has correct properties', function () {
    logger()->debug('This is a test message');

    $file = $this->getTestFile();
    $logFile = new LogFile($file);

    expect($logFile)->toHaveProperties(['filename', 'path', 'fullPath', 'size']);
});

it('has correct filename', function () {
    logger()->debug('This is a test message');

    $file = $this->getTestFile();
    $logFile = new LogFile($file);

    expect($logFile->getFilename())->toBe($file->getFilename());
});

it('has correct path', function () {
    logger()->debug('This is a test message');

    $file = $this->getTestFile();
    $logFile = new LogFile($file);

    expect($logFile->getPath())->toBe($file->getPath());
});

it('has correct full path', function () {
    logger()->debug('This is a test message');

    $file = $this->getTestFile();
    $logFile = new LogFile($file);

    expect($logFile->getFullPath())->toBe($file->getPathname());
});

it('has correct size', function () {
    logger()->debug('This is a test message');

    $file = $this->getTestFile();
    $logFile = new LogFile($file);

    expect($logFile->getSize())->toBe($file->getSize());
});

it('formats readable size', function () {
    logger()->debug('This is a test message');

    $file = $this->getTestFile();
    $logFile = new LogFile($file);

    tap($logFile->getReadableSize(), function ($size) use ($file) {
        expect($size)->toBeString();
        expect($size)->toContain('b');
        expect($size)->toBe($file->getSize().' b');
    });
});

it('returns correct instance', function () {
    logger()->debug('This is a test message');

    $file = $this->getTestFile();
    $logFile = new LogFile($file);

    $content = $logFile->getContent();

    expect($content)->toBeInstanceOf(Collection::class);

    tap($content->first(), function ($record) {
        expect($record)->toBeInstanceOf(LogRecord::class);
    });
});
