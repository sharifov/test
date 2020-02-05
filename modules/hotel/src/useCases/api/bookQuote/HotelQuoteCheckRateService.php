<?php

namespace modules\hotel\src\useCases\api\bookQuote;

use modules\hotel\components\ApiHotelService;
use modules\hotel\models\HotelQuote;
use modules\hotel\models\HotelQuoteRoom;
use sales\auth\Auth;
use sales\repositories\product\ProductQuoteRepository;
use sales\services\TransactionManager;

/**
 * Class HotelQuoteCheckRateService
 *
 * @property  ApiHotelService $apiService
 * @property ProductQuoteRepository $productQuoteRepository
 * @property TransactionManager $transactionManager
 */
class HotelQuoteCheckRateService
{
	public $status = 0; // failed
    public $message = '';
	/**
	 * @var TransactionManager
	 */
	private $transactionManager;
    /**
	 * @var ProductQuoteRepository
	 */
	private $productQuoteRepository;
	/**
	 * @var ApiHotelService
	 */
	private $apiService;

    /**
     * HotelQuoteBookService constructor.
     * @param ProductQuoteRepository $productQuoteRepository
     * @param TransactionManager $transactionManager
     */
    public function __construct(ProductQuoteRepository $productQuoteRepository, TransactionManager $transactionManager)
	{
		$this->apiService = \Yii::$app->getModule('hotel')->apiService;
		$this->productQuoteRepository = $productQuoteRepository;
		$this->transactionManager = $transactionManager;
	}

    /**
     * @param HotelQuote $model
     * @return $this
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     */
    public function checkRate(HotelQuote $model)
    {
        $hotel = HotelQuoteCheckRateGuard::hotel($model);
        $hotelQuoteRooms = HotelQuoteCheckRateGuard::hotelQuoteRooms($model);
        $userId = Auth::id();
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

        $apiResponse = $this->apiService->requestBookingHandler('booking/checkrate', $params, $model->hq_id);

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
            $this->transactionManager->wrap(function () use ($model, $apiResponse, $userId) {
                $productQuote = $model->hqProductQuote;
                $productQuote->error($userId, $apiResponse['message']);
                $this->productQuoteRepository->save($productQuote);
            });
        }
        return $this;
    }
}