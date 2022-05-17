<?php

declare(strict_types=1);

namespace modules\flight\src\helpers;

use modules\product\src\entities\productType\ProductType;
use modules\product\src\repositories\ProductTypeRepository;

class SettingHelper
{
    /**
     * @var array
     */
    private static array $_cache = [];

    /**
     * @return array
     */
    private static function getSettings(): array
    {
        if (!isset(self::$_cache[__FUNCTION__])) {
            $model = ProductTypeRepository::getById(ProductType::PRODUCT_FLIGHT);
            self::$_cache[__FUNCTION__] = $model
                ? json_decode($model->getAttribute('pt_settings'), true) ?? []
                : [];
        }

        return self::$_cache[__FUNCTION__];
    }

    /**
     * @return float
     */
    public static function getProcessingFeeAmount(): float
    {
        return (float) (self::getSettings()['processing_fee_amount'] ?? 0);
    }

    /**
     * @return int
     */
    public static function getExpirationDaysOfNewOffers(): int
    {
        return (int) (self::getSettings()['expiration_days_of_new_offers'] ?? 7);
    }

    /**
     * @return int
     */
    public static function getMinHoursDifferenceOffers(): int
    {
        return (int) (self::getSettings()['expiration_days_of_new_offers'] ?? 24);
    }
}
