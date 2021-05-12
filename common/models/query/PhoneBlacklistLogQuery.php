<?php

namespace common\models\query;

use yii\db\Expression;

/**
* @see \common\models\PhoneBlacklistLog
*/
class PhoneBlacklistLogQuery extends \yii\db\ActiveQuery
{
    /**
    * @return \common\models\PhoneBlacklistLog[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return \common\models\PhoneBlacklistLog|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function byPhone(string $phone): self
    {
        return $this->andWhere(['pbll_phone' => $phone]);
    }

    public function byMinutesPeriod(int $minutes): self
    {
        return $this->andWhere(['>=', 'pbll_created_dt', new Expression('NOW() - INTERVAL :minutes MINUTE', ['minutes' => $minutes])]);
    }
}
