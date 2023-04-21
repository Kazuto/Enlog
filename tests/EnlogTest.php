<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ItemNotFoundException;
use Kazuto\Enlog\Enlog;
use Kazuto\Enlog\Support\LogFile;

it('returns empty collection when calling get() and no logs are present', function () {
    tap((new Enlog())->get(), function ($returnValue) {
        expect($returnValue)->toBeInstanceOf(Collection::class);
        expect($returnValue)->toBeEmpty();
    });
});

it('sorts items when calling sort()', function () {
    tap((new Enlog())->get(), function ($returnValue) {
        expect($returnValue)->toBeInstanceOf(Collection::class);
        expect($returnValue)->toBeEmpty();
    });
});

it('returns collection of LogFile arrays sorted by filename when calling sort()', function () {
    logger()->debug('This is a test message');

    File::copy(
        app()->storagePath('logs/laravel.log'),
        app()->storagePath('logs/local-2023-04-20.log'),
    );

    File::copy(
        app()->storagePath('logs/laravel.log'),
        app()->storagePath('logs/local-2023-04-21.log'),
    );

    tap((new Enlog())->get(), function ($collection) {
        expect($collection)->toBeInstanceOf(Collection::class);
        expect($collection)->toHaveCount(2);
        expect($collection)->toHaveKeys(['laravel', 'local']);

        tap($collection->get('local'), function ($array) {
            expect($array)->toBeArray();
            expect($array)->toHaveCount(2);

            expect(Arr::first($array))->toBeInstanceOf(LogFile::class);
            expect(Arr::first($array)->getFilename())->toEqual('local-2023-04-20.log');

            expect(Arr::last($array))->toBeInstanceOf(LogFile::class);
            expect(Arr::last($array)->getFilename())->toEqual('local-2023-04-21.log');
        });
    });

    tap((new Enlog())->sort()->get(), function ($collection) {
        expect($collection)->toBeInstanceOf(Collection::class);
        expect($collection)->toHaveCount(2);
        expect($collection)->toHaveKeys(['laravel', 'local']);

        tap($collection->get('local'), function ($array) {
            expect($array)->toBeArray();
            expect($array)->toHaveCount(2);

            expect(Arr::first($array))->toBeInstanceOf(LogFile::class);
            expect(Arr::first($array)->getFilename())->toEqual('local-2023-04-21.log');

            expect(Arr::last($array))->toBeInstanceOf(LogFile::class);
            expect(Arr::last($array)->getFilename())->toEqual('local-2023-04-20.log');
        });
    });
});

it('throws exception if no log file present when calling find()', function () {
    (new Enlog())->find('laravel.log');
})->throws(ItemNotFoundException::class);

it('returns single LogFile instance when calling find()', function () {
    logger()->debug('This is a test message');

    expect((new Enlog())->find('laravel.log'))->toBeInstanceOf(LogFile::class);
});
