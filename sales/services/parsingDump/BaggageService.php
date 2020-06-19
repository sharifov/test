<?php

namespace sales\services\parsingDump;



/**
 * Class BaggageService
 */
class BaggageService
{


    public static function searchByIata(array $baggageFromDump, string $departureIata, string $arrivalIata)
    {
        $segmentIata = $departureIata . $arrivalIata;

        \yii\helpers\VarDumper::dump(array_column($baggageFromDump, 'segment'), 10, true); exit();
        /* FOR DEBUG:: must by remove */

        if ($key = array_search($segmentIata, array_column($baggageFromDump, 'segment'), false)) {
            return $key;
        }

    }
}