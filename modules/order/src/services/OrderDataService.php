<?php

namespace modules\order\src\services;

use common\models\Language;
use modules\order\src\entities\orderData\OrderData;
use modules\order\src\entities\orderData\OrderDataRepository;
use yii\helpers\VarDumper;
use yii\validators\StringValidator;

/**
 * Class OrderDataService
 *
 * @property OrderDataRepository $repository
 */
class OrderDataService
{
    private OrderDataRepository $repository;

    public function __construct(OrderDataRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create($orderId, $displayUid, $sourceId, $languageId, $marketCountry, $action, $createdUserId): void
    {
        $orderData = OrderData::create(
            $orderId,
            $displayUid,
            $sourceId,
            $this->getLanguage($orderId, $languageId, $action),
            $this->getMarketCountry($orderId, $marketCountry, $action),
            $createdUserId
        );

        $orderData->detachBehavior('user');

        $this->repository->save($orderData);
    }

    private function getMarketCountry(int $orderId, $marketCountry, string $action): ?string
    {
        if (!$marketCountry) {
            \Yii::warning([
                'message' => 'Market country error',
                'error' => 'Is empty',
                'orderId' => $orderId,
                'action' => $action
            ], 'OrderDataService');
            return null;
        }

        $stringValidator = new StringValidator(['max' => 2, 'min' => 1]);
        if (!$stringValidator->validate($marketCountry, $error)) {
            \Yii::warning([
                'message' => 'Market country error',
                'marketCountry' => VarDumper::dumpAsString($marketCountry),
                'error' => $error,
                'orderId' => $orderId,
                'action' => $action
            ], 'OrderDataService');
            return null;
        }

        if (!array_key_exists($marketCountry, Language::getCountryNames())) {
            \Yii::warning([
                'message' => 'Market country error',
                'marketCountry' => VarDumper::dumpAsString($marketCountry),
                'error' => 'Market country is not found',
                'orderId' => $orderId,
                'action' => $action
            ], 'OrderDataService');
            return null;
        }

        return $marketCountry;
    }

    private function getLanguage(int $orderId, $languageId, string $action): ?string
    {
        if (!$languageId) {
            \Yii::warning([
                'message' => 'Language Id error',
                'error' => 'Is empty',
                'orderId' => $orderId,
                'action' => $action
            ], 'OrderDataService');
            return null;
        }

        $stringValidator = new StringValidator(['max' => 5, 'min' => 1]);
        if (!$stringValidator->validate($languageId, $error)) {
            \Yii::warning([
                'message' => 'Language Id error',
                'languageId' => VarDumper::dumpAsString($languageId),
                'error' => $error,
                'orderId' => $orderId,
                'action' => $action
            ], 'OrderDataService');
            return null;
        }

        $language = Language::find()->andWhere(['language_id' => $languageId])->exists();
        if (!$language) {
            \Yii::warning([
                'message' => 'Language Id error',
                'languageId' => VarDumper::dumpAsString($languageId),
                'error' => 'Language Id is not found',
                'orderId' => $orderId,
                'action' => $action
            ], 'OrderDataService');
            return null;
        }

        return $languageId;
    }
}
