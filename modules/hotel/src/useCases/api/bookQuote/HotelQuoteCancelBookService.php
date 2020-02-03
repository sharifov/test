<?php

namespace modules\hotel\src\useCases\api\searchQuote;

use common\models\Client;
use modules\hotel\components\ApiHotelService;
use modules\hotel\models\HotelQuote;
use modules\hotel\models\HotelQuoteRoom;
use modules\hotel\models\HotelQuoteRoomPax;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use sales\auth\Auth;
use sales\repositories\product\ProductQuoteRepository;
use sales\services\TransactionManager;
use yii\helpers\VarDumper;

/**
 * Class HotelQuoteBookService
 * @package modules\hotel\src\useCases\api\bookQuote
 *
 * @property  ApiHotelService $apiService
 * @property ProductQuoteRepository $productQuoteRepository
 * @property TransactionManager $transactionManager
 */
class HotelQuoteCancelBookService
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
    public function cancelBook(HotelQuote $model)
    {
        $params = ['bookingId' => $model->hq_booking_id];
        $apiResponse = $this->apiService->requestBookingHandler('booking/book', $params, 'delete');
        $userId = Auth::id();

        if ($apiResponse['status']) {
            $this->transactionManager->wrap(function () use ($model, $apiResponse, $userId) {
                $productQuote = $model->hqProductQuote;
                $productQuote->cancelled($userId);
                $this->productQuoteRepository->save($productQuote);

                $model->hq_booking_id = null;
                $model->hq_json_booking = null;
                $model->save();

                HotelQuoteRoom::updateAll(
                    [
                        'hqr_rate_comments' => null,
                        'hqr_rate_comments_id' => null,
                        'hqr_type' => HotelQuoteRoom::TYPE_RECHECK,
                    ],
                    ['hqr_hotel_quote_id' => $model->hq_id]
                );

                $this->status = 1; // success
                $this->message = 'Booking canceled.';
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
}