<?php

namespace modules\rentCar\src\services;

use modules\product\src\entities\productQuote\ProductQuoteRepository;
use modules\rentCar\components\ApiRentCarService;
use modules\rentCar\src\entity\rentCarQuote\RentCarQuote;
use modules\rentCar\src\repositories\rentCar\RentCarQuoteRepository;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class RentCarQuoteCancelBookService
 */
class RentCarQuoteCancelBookService
{
    /**
     * @param RentCarQuote $rentCarQuote
     * @param int|null $userId
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public static function cancelBook(RentCarQuote $rentCarQuote, ?int $userId = null): bool
    {
        self::guard($rentCarQuote);
        $email = self::getEmail($rentCarQuote);

        /** @var ApiRentCarService $apiRentCarService */
        $apiRentCarService = Yii::$app->getModule('rent-car')->apiService;

        $rentCar = $rentCarQuote->rcqRentCar;
        $productQuote = $rentCarQuote->rcqProductQuote;

        $cancelResult = $apiRentCarService->cancel($rentCarQuote->rcq_booking_id, $email, $rentCar->prc_request_hash_key);

        if ($cancelResult['error'] === false) {
            if (!$cancelStatus = ArrayHelper::getValue($cancelResult, 'data.status')) {
                throw new \DomainException('Cancel book is failed. Status not found in response');
            }
            if (strtoupper((string) $cancelStatus) !== 'SUCCESS') {
                throw new \DomainException('Cancel book is failed. Status in response (' . $cancelStatus . ')');
            }
        } else {
            throw new \DomainException('Cancel book is failed. ' . $cancelResult['error']);
        }

        $productQuote->cancelled($userId);
        $rentCarQuote->rcq_booking_id = null;

        $rentCarQuoteRepository = Yii::createObject(RentCarQuoteRepository::class);
        $productQuoteRepository = Yii::createObject(ProductQuoteRepository::class);

        $rentCarQuoteRepository->save($rentCarQuote);
        $productQuoteRepository->save($productQuote);

        return true;
    }

    public static function guard(RentCarQuote $rentCarQuote)
    {
        if (!$rentCarQuote->rcqProductQuote->isBooked()) {
            throw new \DomainException('RentCarQuote not booked');
        }
        if (!$rentCarQuote->rcq_booking_id) {
            throw new \DomainException('BookingId is empty');
        }
        return true;
    }

    /**
     * @param RentCarQuote $rentCarQuote
     * @return string
     * @throws \Exception
     */
    private static function getEmail(RentCarQuote $rentCarQuote): string
    {
        if ($emailAgent = ArrayHelper::getValue($rentCarQuote, 'rcqRentCar.prcProduct.prLead.employee.email')) {
            return $emailAgent;
        }
        if ($emailClient = ArrayHelper::getValue($rentCarQuote, 'rcqRentCar.prcProduct.prLead.client.clientEmails.0')) {
            return $emailClient;
        }
        throw new \DomainException('Email is required');
    }
}
