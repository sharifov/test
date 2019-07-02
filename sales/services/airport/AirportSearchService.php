<?php

namespace sales\services\airport;

use common\models\Airport;
use sales\repositories\airport\AirportRepository;

/**
 * Class AirportSearchService
 * @param AirportRepository $airports
 */
class AirportSearchService
{
    private $airports;

    /**
     * AirportSearchService constructor.
     * @param AirportRepository $airports
     */
    public function __construct(AirportRepository $airports)
    {
        $this->airports = $airports;
    }

    /**
     * @param string $term
     * @return array
     */
    public function search(string $term): array
    {
        $out = [];
        if ($term) {
            $term = mb_strtolower(trim($term));
            /** @var Airport[] $airports */
            $airports = $this->airports->getListForSearch($term);
            $data = [];
            foreach ($airports as $key => $airport) {
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
