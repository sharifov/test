<?php

namespace webapi\src\request;

use webapi\src\services\quote\ProductQuoteRefundService;
use webapi\src\services\quote\ProductQuoteService;

/**
 * Class RequestBoAdditional
 */
class RequestBoAdditionalSources
{
    public const TYPE_PRODUCT_QUOTE = 1;
    public const TYPE_PRODUCT_QUOTE_REFUND = 2;

    public const LIST_NAME = [
        self::TYPE_PRODUCT_QUOTE => 'product_quote',
        self::TYPE_PRODUCT_QUOTE_REFUND => 'product_quote_refund',
    ];

    public const SERVICE_LIST = [
        self::TYPE_PRODUCT_QUOTE => ProductQuoteService::class,
        self::TYPE_PRODUCT_QUOTE_REFUND => ProductQuoteRefundService::class,
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

    public static function getServiceByType(int $type): ?RequestBoInterface
    {
        $className = self::getServiceClassByType($type);
        if (!($object = \Yii::createObject($className)) || !($object instanceof RequestBoInterface)) {
            throw new \RuntimeException('Service must be instanceof RequestBoInterface');
        }
        return $object;
    }

    public static function getServiceClassByType(int $type): string
    {
        if (!$className = self::SERVICE_LIST[$type] ?? null) {
            throw new \RuntimeException('Class service not found by type(' . $type . ')');
        }
        if (!class_exists($className)) {
            throw new \RuntimeException('Class service not found(' . $className . ')');
        }
        return $className;
    }
}
