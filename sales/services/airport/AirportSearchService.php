<?php

namespace sales\services\airport;

use common\models\Airport;
use sales\repositories\airport\AirportRepository;

class AirportSearchService
{
    private $airports;

    public function __construct(AirportRepository $airports)
    {
        $this->airports = $airports;
    }

    public function search($term): array
    {
        $out = [];
        /** @var Airport $airport */
        if (!is_null($term)) {
            $term = mb_strtolower($term);
            $airports = $this->airports->getListForSearch($term);
            $data = [];
            foreach ($airports as $airport) {
                $data[] = [
                    'id' => $airport->iata,
                    'text' => $airport->getText(),
                    'selection' => $airport->getSelection(),
                ];
            }
            $out['results'] = array_values($data);
        }
        return $out;
    }
}
