<?php

namespace modules\hotel\src\useCases\api\bookQuote;

use common\models\Client;
use modules\hotel\components\ApiHotelService;
use modules\hotel\models\HotelQuote;
use modules\hotel\models\HotelQuoteRoomPax;
use modules\hotel\src\entities\hotelQuoteServiceLog\CreateDto;
use modules\hotel\src\entities\hotelQuoteServiceLog\HotelQuoteServiceLog;
use modules\hotel\src\entities\hotelQuoteServiceLog\HotelQuoteServiceLogStatus;
use modules\hotel\src\entities\hotelQuoteServiceLog\HotelQuoteServiceLogStatus as LogStatus;
use modules\lead\src\services\LeadFailBooking;
use sales\auth\Auth;
use sales\helpers\app\AppHelper;
use sales\repositories\product\ProductQuoteRepository;
use sales\services\TransactionManager;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class HotelQuoteBookService
 *
 * @property int $status
 * @property string $message
 *
 * @property  ApiHotelService $apiService
 * @property ProductQuoteRepository $productQuoteRepository
 * @property TransactionManager $transactionManager
 * @property-read LeadFailBooking $leadFailBooking
 */
class HotelQuoteBookService
{
    public $status = 0; // 0 failed : 1 success
    public $message = '';

    private $transactionManager;
    private $productQuoteRepository;
    private $apiService;
    private LeadFailBooking $leadFailBooking;

    /**
     * HotelQuoteBookService constructor.
     * @param ProductQuoteRepository $productQuoteRepository
     * @param TransactionManager $transactionManager
     * @param LeadFailBooking $leadFailBooking
     */
    public function __construct(
        ProductQuoteRepository $productQuoteRepository,
        TransactionManager $transactionManager,
        LeadFailBooking $leadFailBooking
    ) {
        $this->apiService = \Yii::$app->getModule('hotel')->apiService;
        $this->productQuoteRepository = $productQuoteRepository;
        $this->transactionManager = $transactionManager;
        $this->leadFailBooking = $leadFailBooking;
    }

    /**
     * @param HotelQuote $model
     * @return $this
     * @throws \Throwable
     */
    public function book(HotelQuote $model): self
    {
        $rooms = [];
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
            'name' => self::getClientName($model),
            'surname' => self::getClientLastName($model),
            'rooms' => $rooms,
        ];

        $createDto = new CreateDto($model->hq_id, LogStatus::ACTION_TYPE_BOOK, $params);
        $hotelQuoteServiceLog = HotelQuoteServiceLog::create($createDto);

        $apiResponse = $this->apiService->requestBookingHandler('booking/book', $params);

        if ($apiResponse['statusApi'] === HotelQuoteServiceLogStatus::STATUS_SUCCESS) {
            try {
                $this->transactionManager->wrap(function () use ($model, $apiResponse, $productQuote, $userId) {
                    if (!$reference = ArrayHelper::getValue($apiResponse, 'data.reference')) {
                        throw new \RuntimeException('In response from ApiHotelService is missing - data.reference');
                    }

                    $model->hq_booking_id = $reference;
                    $model->hq_json_booking = $apiResponse;
                    $model->saveChanges();

                    $productQuote->booked($userId);
                    $this->productQuoteRepository->save($productQuote);

                    $this->status = 1; // success
                    $this->message = 'Booking confirmed. (BookingId: ' . $model->hq_booking_id . ')';
                });
            } catch (\Throwable $throwable) {
                $this->message = 'Booking confirmed but not saved. Error: ' . $throwable->getMessage();
                Yii::error(AppHelper::throwableLog($throwable), 'HotelQuoteBookService:response:book:success');
            }
        } else {
            $this->message = $apiResponse['message'];
            $productQuote = $model->hqProductQuote;
            $productQuote->error($userId, $apiResponse['message']);
            $this->productQuoteRepository->save($productQuote);
        }

        $hotelQuoteServiceLog->setStatus($apiResponse['statusApi'])
            ->setMessage($apiResponse['data']['logData'])
            ->saveChanges();

        return $this;
    }

    private static function getClientName(HotelQuote $hotelQuote): string
    {
        if ($name = ArrayHelper::getValue($hotelQuote, 'hqProductQuote.pqProduct.holder.ph_first_name')) {
            return $name;
        }
        if ($name = ArrayHelper::getValue($hotelQuote, 'hqProductQuote.pqProduct.prLead.client.first_name')) {
            return $name;
        }
        throw new \DomainException('Client first name not found.');
    }

    private static function getClientLastName(HotelQuote $hotelQuote): string
    {
        if ($surname = ArrayHelper::getValue($hotelQuote, 'hqProductQuote.pqProduct.holder.ph_last_name')) {
            return $surname;
        }
        if ($surname = ArrayHelper::getValue($hotelQuote, 'hqProductQuote.pqProduct.prLead.client.last_name')) {
            return $surname;
        }
        if ($surname = ArrayHelper::getValue($hotelQuote, 'hqProductQuote.pqProduct.prLead.client.full_name')) {
            return $surname;
        }
        throw new \DomainException('Client last name not found.');
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
