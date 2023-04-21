<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Kazuto\Enlog\Support\LogFile;
use Kazuto\Enlog\Support\LogRecord;
use function Pest\Laravel\freezeTime;

it('has correct properties', function () {
    logger()->debug('This is a test message');

    $file = $this->getTestFile();
    $logFile = new LogFile($file);

    $content = $logFile->getContent();

    expect($content)->toBeInstanceOf(Collection::class);

    tap($content->first(), function ($record) {
        expect($record)->toHaveProperties(['date', 'level', 'message', 'body']);
    });
});

it('returns correct record values', function () {
    freezeTime();

    $array = [
        'first_level_first_key' => 'something',
        'first_level_second_key' => [
            'second_level_first_key' => 'another_item',
            'second_level_second_key' => [
                'third_level' => 'another_level',
            ],
        ],
    ];

    logger()->debug('This is a test message', $array);

    $file = $this->getTestFile();
    $logFile = new LogFile($file);

    $content = $logFile->getContent();

    tap($content->first(), function (LogRecord $record) use ($array) {
        $date = Carbon::now()->toDateTimeString();

        expect($record->getDate()->toDateTimeString())->toBe($date);
        expect($record->getLevel())->toEqual('debug');
        expect($record->getMessage())->toEqual('This is a test message');
        expect($record->getBody())->toEqual('testing.DEBUG: This is a test message {"first_level_first_key":"something","first_level_second_key":{"second_level_first_key":"another_item","second_level_second_key":{"third_level":"another_level"}}}');

        tap($record->getContext(), function ($context) use ($array) {
            expect($context)->toBeArray();
            expect($context)->toEqual($array);
        });
    });
});
