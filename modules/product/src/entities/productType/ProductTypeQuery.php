<?php

namespace modules\product\src\entities\productType;

class ProductTypeQuery
{
    /**
     * @return array
     */
    public static function getListAll(): array
    {
        return self::getList(false);
    }

    /**
     * @return array
     */
    public static function getListEnabled(): array
    {
        return self::getList(true);
    }

    /**
     * @param bool $enabled
     * @return array
     */
    private static function getList(bool $enabled): array
    {
        $query = ProductType::find()->select(['pt_name']);

        if ($enabled) {
            $query->enabled();
        }

        return $query->orderBy(['pt_id' => SORT_ASC])->indexBy('pt_id')->asArray()->column();
    }
}
