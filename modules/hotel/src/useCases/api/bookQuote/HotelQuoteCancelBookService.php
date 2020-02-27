<?php

namespace modules\hotel\src\useCases\api\bookQuote;

use modules\hotel\components\ApiHotelService;
use modules\hotel\models\HotelQuote;
use modules\hotel\models\HotelQuoteRoom;
use modules\hotel\src\entities\hotelQuoteServiceLog\CreateDto;
use modules\hotel\src\entities\hotelQuoteServiceLog\HotelQuoteServiceLog;
use modules\hotel\src\entities\hotelQuoteServiceLog\HotelQuoteServiceLogStatus;
use modules\hotel\src\entities\hotelQuoteServiceLog\HotelQuoteServiceLogStatus as LogStatus;
use sales\auth\Auth;
use sales\repositories\product\ProductQuoteRepository;
use sales\services\TransactionManager;


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
    public function cancelBook(HotelQuote $model): self
    {
        $params = ['bookingId' => $model->hq_booking_id];

        $createDto = new CreateDto($model->hq_id,LogStatus::ACTION_TYPE_CANCEL, $params);
        $hotelQuoteServiceLog = HotelQuoteServiceLog::create($createDto);

        $apiResponse = $this->apiService->requestBookingHandler('booking/book', $params, 'delete');
        $userId = Auth::id();

        if ($apiResponse['statusApi'] === HotelQuoteServiceLogStatus::STATUS_SUCCESS) {
            $this->transactionManager->wrap(function () use ($model, $apiResponse, $userId) {
                $productQuote = $model->hqProductQuote;
                $productQuote->cancelled($userId);
                $this->productQuoteRepository->save($productQuote);

                $model->setBookingId('')
                    ->saveChanges();

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

        $hotelQuoteServiceLog->setStatus($apiResponse['statusApi'])
            ->setMessage($apiResponse['data']['logData'])
            ->saveChanges();

        return $this;
    }
}