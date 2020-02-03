<?php

namespace modules\hotel\src\useCases\api\bookQuote;

use modules\hotel\components\ApiHotelService;
use modules\hotel\models\HotelQuote;
use modules\hotel\models\HotelQuoteRoom;

/**
 * Class HotelQuoteCheckRateService
 * @package modules\hotel\src\useCases\api\bookQuote
 *
 * @property  ApiHotelService $apiService
 */
class HotelQuoteCheckRateService
{
	public $status = 0; // failed
    public $message = '';
	/**
	 * @var ApiHotelService
	 */
	private $apiService;

	public function __construct()
	{
		$this->apiService = \Yii::$app->getModule('hotel')->apiService;
	}

    /**
     * @param HotelQuote $model
     * @return $this
     */
    public function checkRate(HotelQuote $model)
    {
        $hotel = HotelQuoteCheckRateGuard::hotel($model);
        $hotelQuoteRooms = HotelQuoteCheckRateGuard::hotelQuoteRooms($model);

        $rooms = [];
        foreach ($hotelQuoteRooms as $hotelQuoteRoom) {
            /* @var $hotelQuoteRoom HotelQuoteRoom */
            if ($hotelQuoteRoom->hqr_type === $hotelQuoteRoom::TYPE_BOOKABLE && !empty($hotelQuoteRoom->hqr_rate_comments_id)) {
                $roomData = [
                    'type' => $hotelQuoteRoom::TYPE_LIST[$hotelQuoteRoom::TYPE_BOOKABLE],
                    'rateCommentsId' => $hotelQuoteRoom->hqr_rate_comments_id,
                ];
            } else {
                $roomData = [
                    'key' => $hotelQuoteRoom->hqr_key,
                    'type' => $hotelQuoteRoom::TYPE_LIST[$hotelQuoteRoom::TYPE_RECHECK],
                ];
            }
            $rooms[] = $roomData;
        }

        $params = [
            'checkIn' => $hotel->ph_check_in_date,
            'rooms' => $rooms,
        ];

        $apiResponse = $this->apiService->requestBookingHandler('booking/checkrate', $params);

        if ($apiResponse['status']) {
            if (count($apiResponse['data']['rooms'])) {
                foreach ($apiResponse['data']['rooms'] as $room) {
                    if ($hotelQuoteRoom = HotelQuoteRoom::findOne(['hqr_key' => $room['key']])){
                        $hotelQuoteRoom->setAdditionalInfo($room);
                    }
                }
            } elseif (!empty($apiResponse['data']['rateComments'])) {
                HotelQuoteRoom::updateAll(
                    ['hqr_rate_comments' => strip_tags($apiResponse['data']['rateComments'])],
                    ['hqr_hotel_quote_id' => $model->hq_id]
                );
            }
            $this->status = 1; // success
            $this->message = 'Check Rate completed successfully. ' . $apiResponse['message'];
        } else {
            $this->message = $apiResponse['message'];
        }
        return $this;
    }
}