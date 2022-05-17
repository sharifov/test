<?php

declare(strict_types=1);

namespace modules\product\src\repositories;

use modules\product\src\entities\productType\ProductType;
use Yii;

class ProductTypeRepository
{
    /**
     * @param int $id
     * @return string
     */
    public static function getCacheKeyById(int $id): string
    {
        return md5(serialize([__FUNCTION__, $id]));
    }

    /**
     * @param int $id
     * @return ProductType|null
     */
    public static function getById(int $id): ?ProductType
    {
        return Yii::$app->cache->getOrSet(self::getCacheKeyById($id), function () use ($id) {
            $model = ProductType::findOne($id);
            if ($model && $model->pt_settings) {
                $model->pt_settings = json_decode($model->getAttribute('pt_settings'), true);
            }
            return ProductType::findOne($id);
        }, 3600);
    }

    /**
     * @param int $id
     * @return bool
     */
    public static function clearCacheById(int $id): bool
    {
        return Yii::$app->cache->delete(self::getCacheKeyById($id));
    }
}
