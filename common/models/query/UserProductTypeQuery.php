<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\UserProductType]].
 *
 * @see \common\models\UserProductType
 */
class UserProductTypeQuery extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return \common\models\UserProductType[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\UserProductType|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
