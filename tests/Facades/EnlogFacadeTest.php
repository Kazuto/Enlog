<?php

use Kazuto\Enlog\Enlog;
use Kazuto\Enlog\Facades\Enlog as EnlogFacade;

it('returns class instance', function () {
    expect(EnlogFacade::partialMock())->toBeInstanceOf(Enlog::class);
});
