<?php

namespace Kazuto\Enlog\Support;

use Illuminate\Support\Carbon;

class LogRecord
{
    const LEVEL_REGEX = '[A-Za-z]+\.(TRACE|DEBUG|INFO|NOTICE|WARN|WARNING|ERROR|SEVERE|FATAL):';

    const MESSAGE_REGEX = '([A-Za-z0-9]+( [A-Za-z0-9]+)+)';

    const CONTEXT_REGEX = '({.*})';

    private Carbon $date;

    private string $level;

    private string $message;

    private ?array $context;

    public function __construct(
        private string $heading,
        private string $body
    ) {
        $this->extractDate();
        $this->extractLevel();
        $this->extractMessage();
        $this->extractContext();
    }

    public function getDate(): Carbon
    {
        return $this->date;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    private function extractDate(): void
    {
        $this->date = Carbon::parse(
            str($this->heading)
                ->replace(['[', ']'], '')
                ->toString()
        );
    }

    private function extractLevel(): void
    {
        $this->level = str($this->body)
            ->match('/'.self::LEVEL_REGEX.'/')
            ->lower()
            ->toString();
    }

    private function extractMessage(): void
    {
        $this->message = str($this->body)
            ->match('/'.self::MESSAGE_REGEX.'/')
            ->toString();
    }

    private function extractContext(): void
    {
        $context = str($this->body)
            ->match('/'.self::CONTEXT_REGEX.'/')
            ->toString();

        $this->context = json_decode($context, true);
    }
}
