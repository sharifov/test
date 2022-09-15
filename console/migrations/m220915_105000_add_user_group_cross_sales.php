<?php

use common\models\UserGroup;
use yii\db\Migration;

/**
 * Class m220915_105000_add_user_group_cross_sales
 */
class m220915_105000_add_user_group_cross_sales extends Migration
{
    public const KEY = 'cross_sales';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $model = new UserGroup();
        $model->ug_key = self::KEY;
        $model->ug_name = 'Cross Sales';
        $model->ug_description = 'Cross Sales';
        $model->ug_disable = 0;

        if ($model->save() === false) {
            throw new \RuntimeException('Cant create new User Group');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        UserGroup::deleteAll([
            'ug_key' => self::KEY,
        ]);
    }
}
