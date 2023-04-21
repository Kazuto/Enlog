<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ItemNotFoundException;
use Kazuto\Enlog\Enlog;
use Kazuto\Enlog\Support\Aggregator;
use Kazuto\Enlog\Support\LogFile;

it('returns empty collection if no log file present when calling get()', function () {
    tap((new Aggregator())->get(), function ($collection) {
        expect($collection)->toBeInstanceOf(Collection::class);
        expect($collection)->toBeEmpty();
    });
});

it('returns collection of LogFile arrays when calling get()', function () {
    logger()->debug('This is a test message');

    tap((new Aggregator())->get(), function ($collection) {
        expect($collection)->toBeInstanceOf(Collection::class);
        expect($collection)->toHaveCount(1);

        tap($collection->first(), function ($array) {
            expect($array)->toBeArray();
            expect($array)->toHaveCount(1);

            expect(Arr::first($array))->toBeInstanceOf(LogFile::class);
            expect(Arr::first($array)->getFilename())->toEqual('laravel.log');
        });
    });
});

it('returns collection of indexed LogFile arrays when calling get()', function () {
    logger()->debug('This is a test message');

    File::copy(
        app()->storagePath('logs/laravel.log'),
        app()->storagePath('logs/local-2023-04-20.log'),
    );

    File::copy(
        app()->storagePath('logs/laravel.log'),
        app()->storagePath('logs/local-2023-04-21.log'),
    );

    File::copy(
        app()->storagePath('logs/laravel.log'),
        app()->storagePath('logs/production-2023-04-21.log'),
    );

    tap((new Aggregator())->get(), function ($collection) {
        expect($collection)->toBeInstanceOf(Collection::class);
        expect($collection)->toHaveCount(3);
        expect($collection)->toHaveKeys(['laravel', 'local', 'production']);

        tap($collection->get('laravel'), function ($array) {
            expect($array)->toBeArray();
            expect($array)->toHaveCount(1);

            expect(Arr::first($array))->toBeInstanceOf(LogFile::class);
            expect(Arr::first($array)->getFilename())->toEqual('laravel.log');
        });

        tap($collection->get('local'), function ($array) {
            expect($array)->toBeArray();
            expect($array)->toHaveCount(2);

            expect(Arr::first($array))->toBeInstanceOf(LogFile::class);
            expect(Arr::first($array)->getFilename())->toEqual('local-2023-04-20.log');

            expect(Arr::last($array))->toBeInstanceOf(LogFile::class);
            expect(Arr::last($array)->getFilename())->toEqual('local-2023-04-21.log');
        });

        tap($collection->get('production'), function ($array) {
            expect($array)->toBeArray();
            expect($array)->toHaveCount(1);

            expect(Arr::first($array))->toBeInstanceOf(LogFile::class);
            expect(Arr::first($array)->getFilename())->toEqual('production-2023-04-21.log');
        });
    });
});

it('sorts items when calling sort()', function () {
    logger()->debug('This is a test message');

    File::copy(
        app()->storagePath('logs/laravel.log'),
        app()->storagePath('logs/local-2023-04-20.log'),
    );

    File::copy(
        app()->storagePath('logs/laravel.log'),
        app()->storagePath('logs/local-2023-04-21.log'),
    );

    tap((new Aggregator())->get(), function ($collection) {
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

    tap((new Aggregator())->sort()->get(), function ($collection) {
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
    (new Aggregator())->find('laravel.log');
})->throws(ItemNotFoundException::class);

it('returns single LogFile instance when calling find()', function () {
    logger()->debug('This is a test message');

    expect((new Aggregator())->find('laravel.log'))->toBeInstanceOf(LogFile::class);
});
