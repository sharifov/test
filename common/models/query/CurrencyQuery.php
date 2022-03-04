<?php

namespace common\models\query;

use common\models\Currency;

/**
 * This is the ActiveQuery class for [[Currency]].
 *
 * @see Currency
 */
class CurrencyQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Currency[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Currency|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function byCode(string $code): self
    {
        return $this->andWhere(['cur_code' => $code]);
    }

    public function addCache(int $cacheDuration = 30): self
    {
        return $this->cache($cacheDuration);
    }

    public static function getCurrencySymbolByCode(string $code, int $cacheDuration = 30): string
    {
        return Currency::find()->select(['cur_symbol'])->byCode($code)->addCache($cacheDuration)->scalar();
    }

    public static function existsByCurrencyCode(string $code): bool
    {
        return Currency::find()->byCode($code)->exists();
    }
}
