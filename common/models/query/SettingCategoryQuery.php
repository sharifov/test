<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\SettingCategory]].
 *
 * @see \common\models\SettingCategory
 */
class SettingCategoryQuery extends \yii\db\ActiveQuery
{
    /**
     * @return SettingCategoryQuery
     */
    public function active()
    {
        return $this->andWhere('[[sc_enabled]]=1');
    }

    /**
     * {@inheritdoc}
     * @return \common\models\SettingCategory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\SettingCategory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
