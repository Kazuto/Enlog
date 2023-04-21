<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Kazuto\Enlog\Support\Parser;

it('returns empty collection if no data is passed', function () {
    tap(Parser::parse(''), function ($returnValue) {
        expect($returnValue)->toBeInstanceOf(Collection::class);
        expect($returnValue)->toBeEmpty();
    });
});

it('returns empty collection if non-matching data is passed', function () {
    tap(Parser::parse('lorem ipsum dolor sit amet'), function ($returnValue) {
        expect($returnValue)->toBeInstanceOf(Collection::class);
        expect($returnValue)->toBeEmpty();
    });
});

it('returns filled collection', function (...$content) {
    tap(Parser::parse(Arr::join($content, ' ')), function ($returnValue) use ($content) {
        expect($returnValue)->toBeInstanceOf(Collection::class);
        expect($returnValue)->toHaveCount(count($content));
    });
})->with([
    'one record' => [
        '[2023-04-05 11:21:32] local.ERROR: Unresolvable dependency resolving',
    ],
    'three records' => [
        '[2023-04-05 11:21:32] local.ERROR: Unresolvable dependency resolving',
        '[2023-04-05 11:50:28] local.ERROR: Call to undefined function',
        '[2023-04-05 11:51:58] local.ERROR: Cannot use int as default value',
    ],
    'five records' => [
        '[2023-04-05 11:21:32] local.ERROR: Unresolvable dependency resolving',
        '[2023-04-05 11:50:28] local.ERROR: Call to undefined function isNumberInput()',
        '[2023-04-05 11:51:58] local.ERROR: Cannot use int as default value',
        '[2023-04-05 12:03:51] local.ERROR: syntax error, unexpected double-quote mark',
        '[2023-04-05 12:48:57] local.ERROR: syntax error, unexpected token',
    ],
]);
