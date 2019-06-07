<?php

namespace sales\helpers\airport;

class AirportFormatHelper
{
    public static function formatRows(array $airports, string $term): array
    {
        if (!isset($airports['results']) || !is_array($airports['results'])) {
            return $airports;
        }
        foreach ($airports['results'] as $key => $airport) {
            $airports['results'][$key]['text'] = self::formatText($airport['text'], $term);
        }
        return $airports;
    }

    public static function formatText($str, $term)
    {
        return str_ireplace($term, '<b style="color: #dd0000"><u>' . $term . '</u></b>', $str);
    }

}
