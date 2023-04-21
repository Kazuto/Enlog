<?php

use Illuminate\Support\Collection;
use Kazuto\Enlog\Enlog;
use Kazuto\Enlog\Facades\Enlog as EnlogFacade;
use Kazuto\Enlog\Support\LogFile;

it('returns class instance', function () {
    expect(EnlogFacade::partialMock())->toBeInstanceOf(Enlog::class);
});
