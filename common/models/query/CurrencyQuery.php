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

    public static function getCurrencySymbolByCode(string $code): string
    {
        return Currency::find()->select(['cur_symbol'])->byCode($code)->scalar();
    }
}
