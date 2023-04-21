<?php

namespace Kazuto\Enlog\Support;

use Illuminate\Support\Collection;

class Parser
{
    const DATE_REGEX = "\[\d{4}-[\d{2}-]+\s[\d{2}:]+\]";

    public static function parse(string $content): Collection
    {
        $entries = [];

        preg_match_all('/'.self::DATE_REGEX.'/', $content, $headings);

        $data = preg_split('/'.self::DATE_REGEX.'/', $content);

        if ($data[0] < 1) {
            $trash = array_shift($data);

            unset($trash);
        }

        foreach ($headings as $heading) {
            $count = count($heading);

            for ($i = 0; $i < $count; $i++) {
                $entries[] = [
                    'heading' => $heading[$i],
                    'body' => trim($data[$i])
                ];
            }
        }

        return collect($entries);
    }
}
