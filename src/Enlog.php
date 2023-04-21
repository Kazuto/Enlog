<?php

namespace Kazuto\Enlog;

use Illuminate\Support\Collection;
use Illuminate\Support\ItemNotFoundException;
use Kazuto\Enlog\Support\Aggregator;
use Kazuto\Enlog\Support\LogFile;

class Enlog
{
    private Aggregator $aggregator;

    public function __construct(
        protected ?string $environment = null
    ) {
        $this->aggregator = new Aggregator();
    }

    public function get(): Collection
    {
        return $this->aggregator->get();
    }

    public function sort(bool $desc = true): self
    {
        $this->aggregator = $this->aggregator->sort($desc);

        return $this;
    }

    /**
     * @throws ItemNotFoundException
     */
    public function find(string $fileName): LogFile
    {
        return $this->aggregator->find($fileName);
    }
}
