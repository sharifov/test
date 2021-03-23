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
        return ArrayHelper::getValue($data, 'partner.name');
    }

    public static function getVendorLogo(array $data): ?string
    {
        return ArrayHelper::getValue($data, 'partner.logo');
    }

    public static function getBasePrice(array $data): ?string
    {
        return ArrayHelper::getValue($data, 'price_details.base_price');
    }

    public static function getTotalPrice(array $data): ?string
    {
        return ArrayHelper::getValue($data, 'price_details.total_price');
    }

    public static function getBaseType(array $data): ?string
    {
        return ArrayHelper::getValue($data, 'price_details.base_type');
    }

    public static function getNumRentalDays(array $data): ?string
    {
        return ArrayHelper::getValue($data, 'price_details.num_rental_days');
    }

    public static function getPriceCurrencySymbol(array $data): ?string
    {
        return ArrayHelper::getValue($data, 'price_details.display_symbol');
    }

    public static function getPriceCurrencyCode(array $data): ?string
    {
        return ArrayHelper::getValue($data, 'price_details.currency');
    }

    public static function getPriceTotal(array $data): ?string
    {
        return ArrayHelper::getValue($data, 'price_details.total_price');
    }

    public static function getModelName(array $data): ?string
    {
        return ArrayHelper::getValue($data, 'car.example');
    }

    public static function getModelImg(array $data): ?string
    {
        return ArrayHelper::getValue($data, 'car.images.SIZE268X144') ??
            ArrayHelper::getValue($data, 'car.imageurl');
    }

    public static function getModelCategory(array $data): ?string
    {
        return ArrayHelper::getValue($data, 'car.type_name');
    }

    public static function getPickUpLocation(array $data): ?string
    {
        return ArrayHelper::getValue($data, 'pickup.location');
    }

    public static function getDropOffLocation(array $data): ?string
    {
        return ArrayHelper::getValue($data, 'dropoff.location');
    }

    public static function getOfferToken(array $data, string $requestHash = ''): ?string
    {
        return $requestHash . '_' . md5(ArrayHelper::getValue($data, 'car_reference_id'));
    }

    public static function getOptions(array $data): array
    {
        return self::getActionable($data);
    }

    public static function getActionable(array $data): array
    {
        return [
            'passengers' => ArrayHelper::getValue($data, 'car.passengers'),
            'doors' => ArrayHelper::getValue($data, 'car.doors'),
            'bags' => ArrayHelper::getValue($data, 'car.bags'),
            'automatic_transmission' => ArrayHelper::getValue($data, 'car.automatic_transmission') === 'true' ? 'Yes' : 'No',
            'air_conditioning' => ArrayHelper::getValue($data, 'car.air_conditioning') === 'true' ? 'Yes' : 'No',
        ];
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

    public static function getCarReferenceId(array $data)
    {
        return ArrayHelper::getValue($data, 'car_reference_id');
    }
}
