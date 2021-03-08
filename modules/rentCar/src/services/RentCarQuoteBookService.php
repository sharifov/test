<?php

namespace modules\rentCar\src\services;

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
    public static function book(RentCarQuote $rentCarQuote, ?int $creatorId, bool $preBookingCheck = true): bool
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
        $rentCarQuote->rcq_contract_request_json = $dataResult['data'];

        $client = $rentCarQuote->rcqRentCar->prcProduct->prLead->client;
        $firstName = $client->first_name;
        $lastName = $client->last_name;

        $bookResult = $apiRentCarService->book($referenceId, $firstName, $lastName, $rentCar->prc_request_hash_key);

        /* TODO:: uncomment after book success */
        /*if ($bookResult['error'] === false) {
            if (!$bookingId = ArrayHelper::getValue($dataResult, 'data.results.booking_id')) {
                throw new \DomainException('Book request is error. BookingId not found in response.');
            }
        } else {
            throw new \DomainException('Book request is fail. ' . $dataResult['error']);
        }*/

        $bookingId = random_int(1000, 9999); /* TODO:: FOR DEBUG:: must by remove  */

        $rentCarQuoteRepository = Yii::createObject(RentCarQuoteRepository::class);
        $productQuoteRepository = Yii::createObject(ProductQuoteRepository::class);

        $rentCarQuote->rcq_booking_json = $bookResult;
        $rentCarQuote->rcq_booking_id = $bookingId;
        $rentCarQuoteRepository->save($rentCarQuote);

        $productQuote->booked($creatorId);
        $productQuoteRepository->save($productQuote);

        return true;
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
}
