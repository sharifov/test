<?php

namespace modules\product\src\entities\productOption;

use yii\db\Expression;
use yii\helpers\ArrayHelper;

class ProductOptionQuery
{
    /**
     * @param bool $enabled
     * @param int|null $productTypeId
     * @return array
     */
    public static function getList(bool $enabled = true, ?int $productTypeId = null): array
    {
        $query = ProductOption::find()->orderBy(['po_id' => SORT_ASC]);

        if ($enabled) {
            $query->enabled();
        }

        if ($productTypeId !== null) {
            $query->andWhere(['po_product_type_id' => $productTypeId]);
        }

        $data = $query->asArray()->all();

        return ArrayHelper::map($data, 'po_id', 'po_name');
    }

    public static function getNameByRegexKey(string $key): ?string
    {
        return ProductOption::find()->select(['po_name'])->where(new Expression("REGEXP_REPLACE(LOWER(po_key), '[^a-zA-Z0-9]+', '') = :key", [
            'key' => strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $key))
        ]))->scalar();
    }
}
