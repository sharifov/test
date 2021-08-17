<?php

namespace webapi\src\request;

use modules\product\src\services\ProductQuoteService;
use sales\interfaces\BoWebhookService;
use webapi\src\forms\boWebhook\FlightRefundUpdateForm;
use webapi\src\forms\boWebhook\ReprotectionUpdateForm;
use webapi\src\services\flight\FlightManageApiService;
use yii\base\Model;

class BoWebhook
{
    public const TYPE_REPROTECTION_UPDATE = 1;
    public const TYPE_FLIGHT_REFUND_UPDATE = 2;

    public const LIST_NAME = [
        self::TYPE_REPROTECTION_UPDATE => 'reprotection_update',
        self::TYPE_FLIGHT_REFUND_UPDATE => 'flight_refund_update'
    ];

    public const FORM_LIST = [
        self::TYPE_REPROTECTION_UPDATE => ReprotectionUpdateForm::class,
        self::TYPE_FLIGHT_REFUND_UPDATE => FlightRefundUpdateForm::class
    ];

    public const SERVICE_LIST = [
        self::TYPE_REPROTECTION_UPDATE => ProductQuoteService::class,
        self::TYPE_FLIGHT_REFUND_UPDATE => FlightManageApiService::class
    ];

    public static function getTypeIdByName(string $name): ?int
    {
        $id = array_search($name, self::LIST_NAME);
        return $id ? (int)$id : null;
    }

    public static function getTypeNameById(int $type): ?string
    {
        return self::LIST_NAME[$type] ?? null;
    }

    public static function getFormByType(int $type): ?Model
    {
        return self::FORM_LIST[$type] ? \Yii::createObject(self::FORM_LIST[$type]) : null;
    }

    public static function getServiceByType(int $type): ?BoWebhookService
    {
        return self::SERVICE_LIST[$type] ? \Yii::createObject(self::SERVICE_LIST[$type]) : null;
    }
}
