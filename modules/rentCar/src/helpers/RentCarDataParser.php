<?php

namespace modules\rentCar\src\helpers;

use modules\rentCar\src\entity\rentCar\RentCar;
use yii\helpers\ArrayHelper;

/**
 * Class RentCarDataParser
 */
class RentCarDataParser
{
    public static function prepareDataList(array $data): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (($typename = ArrayHelper::getValue($value, '__typename')) && $typename === 'CarOfferCard') {
                $result[] = $value;
            }
        }
        return $result;
    }

    public static function getVendorName(array $data): ?string
    {
        return ArrayHelper::getValue($data, 'vendor.image.description');
    }

    public static function getVendorLogo(array $data): ?string
    {
        return ArrayHelper::getValue($data, 'vendor.image.url');
    }

    public static function getPricePerDay(array $data): ?string
    {
        return ArrayHelper::getValue($data, 'priceSummary.lead.price.amount');
    }

    public static function getPriceCurrencySymbol(array $data): ?string
    {
        return ArrayHelper::getValue($data, 'priceSummary.total.price.currencyInfo.symbol');
    }

    public static function getPriceCurrencyCode(array $data): ?string
    {
        return ArrayHelper::getValue($data, 'priceSummary.total.price.currencyInfo.code');
    }

    public static function getPriceTotal(array $data): ?string
    {
        return ArrayHelper::getValue($data, 'priceSummary.total.price.amount');
    }

    public static function getModelName(array $data): ?string
    {
        return ArrayHelper::getValue($data, 'vehicle.description');
    }

    public static function getModelImg(array $data): ?string
    {
        return ArrayHelper::getValue($data, 'vehicle.image.url');
    }

    public static function getModelCategory(array $data): ?string
    {
        return ArrayHelper::getValue($data, 'vehicle.category');
    }

    public static function getPickUpLocation(array $data): ?string
    {
        return ArrayHelper::getValue($data, 'tripLocations.pickUpLocation.text');
    }

    public static function getDropOffLocation(array $data): ?string
    {
        return ArrayHelper::getValue($data, 'tripLocations.dropOffLocation.text');
    }

    public static function getOfferToken(array $data, string $requestHash = ''): ?string
    {
        return $requestHash . '_' . ArrayHelper::getValue($data, 'detailsContext.carOfferToken');
    }

    public static function getOptions(array $data): array
    {
        $result = [];
        foreach (ArrayHelper::getValue($data, 'vehicle.attributes', []) as $value) {
            if (($description = ArrayHelper::getValue($value, 'icon.description')) && !empty($value['text'])) {
                $result[$description] = $value['text'];
            }
        }
        return $result;
    }

    public static function getActionable(array $data): array
    {
        $result = [];
        foreach (ArrayHelper::getValue($data, 'actionableConfidenceMessages', []) as $value) {
            if (!empty($value['text'])) {
                $result[] = $value['text'];
            }
        }
        return $result;
    }

    public static function findQuoteByToken(array $data, string $token, string $requestHash = ''): array
    {
        foreach ($data as $value) {
            if (self::getOfferToken($value, $requestHash) === $token) {
                return $value;
            }
        }
        return [];
    }

    public static function getAttributeValue(array $data, string $attribute): ?string
    {
        return ArrayHelper::getValue(self::getOptions($data), $attribute);
    }
}
