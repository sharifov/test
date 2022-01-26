<?php

namespace modules\featureFlag\src\entities;

/**
* @see FeatureFlag
*/
class FeatureFlagQuery extends \yii\db\ActiveQuery
{
    /**
    * @return FeatureFlag[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return FeatureFlag|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
