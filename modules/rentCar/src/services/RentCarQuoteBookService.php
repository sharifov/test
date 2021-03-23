<?php

namespace modules\rentCar\src\services;

use common\models\ClientEmail;
use common\models\ClientPhone;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use modules\rentCar\components\ApiRentCarService;
use modules\rentCar\src\entity\rentCarQuote\RentCarQuote;
use modules\rentCar\src\repositories\rentCar\RentCarQuoteRepository;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class RentCarQuoteBookService
 */
class RentCarQuoteBookService
{
    public static function book(RentCarQuote $rentCarQuote, ?int $creatorId, bool $preBookingCheck = true)
    {
        $rentCar = $rentCarQuote->rcqRentCar;
        $productQuote = $rentCarQuote->rcqProductQuote;
        /** @var ApiRentCarService $apiRentCarService */
        $apiRentCarService = Yii::$app->getModule('rent-car')->apiService;
        $referenceId = $rentCarQuote->rcq_car_reference_id;

        $dataResult = \Yii::$app->cacheFile->get($referenceId);

        if ($dataResult === false) {
            $dataResult = $apiRentCarService->contractRequest($referenceId, $rentCar->prc_request_hash_key);
            \Yii::$app->cacheFile->set($referenceId, $dataResult, 60);
        }
        if ($dataResult['error'] === false) {
            $contractStatus = ArrayHelper::getValue($dataResult, 'data.contract_status');
            if (strtoupper((string) $contractStatus) !== 'SUCCESS') {
                throw new \DomainException('Contract request is error. Quote not created');
            }
        } else {
            throw new \DomainException('Contract request is fail. ' . $dataResult['error']);
        }

        $dataResult = self::prepareContractRequestJson($dataResult);

        if (!$carBookBundle = ArrayHelper::getValue($dataResult, 'data.car_book_bundle')) {
            throw new \DomainException('Contract request is error. car_book_bundle not found in response');
        }

        $rentCarQuote->rcq_contract_request_json = $dataResult['data'];

        $firstName = self::getFirstName($rentCarQuote);
        $lastName = self::getLastName($rentCarQuote);
        $email = self::getEmail($rentCarQuote);
        $phone = self::getPhone($rentCarQuote);

        $bookResult = $apiRentCarService->book($carBookBundle, $firstName, $lastName, $phone, $email, $rentCar->prc_request_hash_key);

        if ($bookResult['error'] === false) {
            if (!$bookingStatus = ArrayHelper::getValue($bookResult, 'data.booking_status')) {
                throw new \DomainException('Book request is failed. Booking status not found in response.');
            }
            if ($bookingStatus !== strtoupper('SUCCESS')) {
                throw new \DomainException('Book request is failed. Booking status is ' . $bookingStatus);
            }
            if (!$bookingId = ArrayHelper::getValue($bookResult, 'data.booking_id')) {
                throw new \DomainException('Book request is failed. BookingId not found in response.');
            }
        } else {
            throw new \DomainException('Book request is fail. ' . $bookResult['error']);
        }

        $rentCarQuoteRepository = Yii::createObject(RentCarQuoteRepository::class);
        $productQuoteRepository = Yii::createObject(ProductQuoteRepository::class);

        $rentCarQuote->rcq_booking_json = $bookResult;
        $rentCarQuote->rcq_booking_id = $bookingId;
        $rentCarQuoteRepository->save($rentCarQuote);

        $productQuote->booked($creatorId);
        $productQuoteRepository->save($productQuote);

        return $bookingId;
    }

    public static function guard(RentCarQuote $rentCarQuote)
    {
        if (!$order = ArrayHelper::getValue($rentCarQuote, 'rcqProductQuote.pqOrder')) {
            throw new \DomainException('Not found Order');
        }
        return true;
    }

    /**
     * @param array $dataResult
     * @return array
     */
    private static function prepareContractRequestJson(array $dataResult): array
    {
        if (isset($dataResult['data']['html'])) {
            unset($dataResult['data']['html']);
        }
        if (isset($dataResult['data']['cdw']['html'])) {
            unset($dataResult['data']['cdw']['html']);
        }
        return $dataResult;
    }

    private static function getFirstName(RentCarQuote $rentCarQuote): string
    {
        if ($biFirstName = ArrayHelper::getValue($rentCarQuote, 'rcqProductQuote.pqOrder.billingInfo.0.bi_first_name')) {
            return $biFirstName;
        }
        if ($clientFirstName = ArrayHelper::getValue($rentCarQuote, 'rcqRentCar.prcProduct.prLead.client.first_name')) {
            return $clientFirstName;
        }
        throw new \DomainException('FirstName not found');
    }

    private static function getLastName(RentCarQuote $rentCarQuote): string
    {
        if ($biLastName = ArrayHelper::getValue($rentCarQuote, 'rcqProductQuote.pqOrder.billingInfo.0.bi_last_name')) {
            return $biLastName;
        }
        if ($clientLastName = ArrayHelper::getValue($rentCarQuote, 'rcqRentCar.prcProduct.prLead.client.last_name')) {
            return $clientLastName;
        }
        throw new \DomainException('LastName not found');
    }

    private static function getEmail(RentCarQuote $rentCarQuote): string
    {
        if ($biEmail = ArrayHelper::getValue($rentCarQuote, 'rcqProductQuote.pqOrder.billingInfo.0.bi_contact_email')) {
            return $biEmail;
        }
        if (
            ($clientId = ArrayHelper::getValue($rentCarQuote, 'rcqRentCar.prcProduct.prLead.client_id')) &&
            $clientEmail = ClientEmail::getGeneralEmail($clientId)
        ) {
            return $clientEmail;
        }
        throw new \DomainException('ClientEmail not found');
    }

    private static function getPhone(RentCarQuote $rentCarQuote)
    {
        if ($biPhone = ArrayHelper::getValue($rentCarQuote, 'rcqProductQuote.pqOrder.billingInfo.0.bi_contact_phone')) {
            return $biPhone;
        }
        if (
            ($clientId = ArrayHelper::getValue($rentCarQuote, 'rcqRentCar.prcProduct.prLead.client_id')) &&
            $clientEmail = ClientPhone::getGeneralPhone($clientId)
        ) {
            return $clientEmail;
        }
        throw new \DomainException('ClientPhone not found');
    }
}
