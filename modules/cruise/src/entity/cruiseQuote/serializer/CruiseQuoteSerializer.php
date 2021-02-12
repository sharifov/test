<?php

namespace modules\cruise\src\entity\cruiseQuote\serializer;

use modules\cruise\src\entity\cruiseQuote\CruiseQuote;
use sales\entities\serializer\Serializer;

/**
 * Class CruiseQuoteSerializer
 *
 * @property CruiseQuote $model
 */
class CruiseQuoteSerializer extends Serializer
{
    public function __construct(CruiseQuote $model)
    {
        parent::__construct($model);
    }

    public static function fields(): array
    {
        return [];
    }

    public function getData(): array
    {
        $data = [];

//        $data['hotel'] = $this->model->hqHotelList ? $this->model->hqHotelList->serialize() : [];
//
//        if ($this->model->hotelQuoteRooms) {
//            $data['rooms'] = [];
//            foreach ($this->model->hotelQuoteRooms as $hotelQuoteRoom) {
//                $data['rooms'][] = $hotelQuoteRoom->serialize();
//            }
//        }

        return $data;
    }
}
