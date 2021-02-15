<?php

namespace modules\hotel\src\useCases\api\searchQuote;

use modules\hotel\components\ApiHotelService;
use modules\hotel\models\Hotel;
use yii\helpers\ArrayHelper;

/**
 * Class HotelQuoteSearchService
 * @package modules\hotel\src\useCases\api\searchQuote
 *
 * @property  ApiHotelService $apiService
 */
class HotelQuoteSearchService
{
    /**
     * @var ApiHotelService
     */
    private $apiService;

    public function __construct()
    {
        $this->apiService = \Yii::$app->getModule('hotel')->apiService;
    }

    /**
     * @param Hotel $hotel
     * @return array|bool|mixed
     */
    public function search(Hotel $hotel)
    {
        $params = [];
        $rooms = [];

        if ($hotel->hotelRooms) {
            foreach ($hotel->hotelRooms as $room) {
                $rooms[] = $room->getDataSearch();
            }
        }

        if ($hotel->ph_max_price_rate) {
            $params['maxRate'] = $hotel->ph_max_price_rate;
        }

        if ($hotel->ph_min_price_rate) {
            $params['minRate'] = $hotel->ph_min_price_rate;
        }

        $keyCache = $hotel->ph_request_hash_key;
        $result = \Yii::$app->cacheFile->get($keyCache);

        if ($result === false) {
            $response = $this->apiService->search($hotel->ph_check_in_date, $hotel->ph_check_out_date, $hotel->ph_destination_code, $rooms, $params);

            if (isset($response['data']['hotels'])) {
                $result = $response['data'];
                $result['hotels'] = array_values(array_filter($result['hotels'], static function ($hotel) {
                    return !empty($hotel['rooms']);
                }));
                \Yii::$app->cacheFile->set($keyCache, $result, 600);
            } else {
                $result = isset($response['error']) ? $response : ['error' => 'Unknown error has occurred.'];
//                \Yii::error('Not found response[data][hotels]', 'useCases:api:searchQuote:HotelQuoteSearchService:search');
            }
        }

        return $result;
    }

    public function clearCache(string $key): void
    {
        \Yii::$app->cacheFile->delete($key);
    }
}
