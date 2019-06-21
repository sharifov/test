<?php

namespace sales\helpers\airport;

class AirportFormatHelper
{
    /**
     * @param array $airports
     * @param string $term
     * @return array
     */
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

    /**
     * @param string $str
     * @param string $term
     * @return string
     */
    public static function formatText(string $str, string $term): string
    {
        return preg_replace('~'.$term.'~i', "<b style=\"color: #e15554\"><u>\$0</u></b>", $str);
    }

}
