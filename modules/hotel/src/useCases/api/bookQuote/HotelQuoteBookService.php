<?php

namespace modules\hotel\src\useCases\api\bookQuote;

use common\models\Client;
use modules\hotel\components\ApiHotelService;
use modules\hotel\models\HotelQuote;
use modules\hotel\models\HotelQuoteRoomPax;
use sales\auth\Auth;
use sales\repositories\product\ProductQuoteRepository;
use sales\services\TransactionManager;

/**
 * Class HotelQuoteBookService
 *
 * @property  ApiHotelService $apiService
 * @property ProductQuoteRepository $productQuoteRepository
 * @property TransactionManager $transactionManager
 */
class HotelQuoteBookService
{
	public $status = 0;
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
     */
    public function book(HotelQuote $model)
    {
        $rooms = [];
        $client = $this->getClient($model);
        $productQuote = $model->hqProductQuote;
        $userId = Auth::id();
        $productQuote->inProgress($userId);
        $this->productQuoteRepository->save($productQuote);

        foreach ($model->hotelQuoteRooms as $quoteRoom) {
            $rooms[] = [
                'key' => $quoteRoom->hqr_key,
                'paxes' => HotelQuoteRoomPax::preparePaxesForBook($quoteRoom->hqr_id)
            ];
        }

        $params = [
            'name' => $client->first_name,
            'surname' => $client->last_name ?: $client->full_name,
            'rooms' => $rooms,
        ];

        $apiResponse = $this->apiService->requestBookingHandler('booking/book', $params);

        if ($apiResponse['status']) {
            $this->transactionManager->wrap(function () use ($model, $apiResponse, $productQuote, $userId) {
                $model->hq_booking_id = $apiResponse['data']['reference'];
                $model->hq_json_booking = $apiResponse['data']['source'];
                $model->save();

                $productQuote->booked($userId);
                $this->productQuoteRepository->save($productQuote);

                $this->status = 1; // success
                $this->message = 'Booking confirmed. (BookingId: ' . $model->hq_booking_id . ')';
            });
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

    /**
     * @param HotelQuote $model
     * @return Client
     */
    private function getClient(HotelQuote $model): Client
    {
        return $model->hqProductQuote->pqProduct->prLead->client;
    }
}