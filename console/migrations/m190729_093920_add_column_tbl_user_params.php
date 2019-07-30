<?php

use yii\db\Migration;

/**
 * Class m190729_093920_add_column_tbl_user_profile
 */
class m190729_093920_add_column_tbl_user_params extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_params}}', 'up_call_expert_limit', $this->smallInteger()->defaultValue(-1));
        Yii::$app->db->getSchema()->refreshTableSchema('{{%user_params}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        \common\models\UserParams::updateAll(['up_call_expert_limit' => 10]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_params}}', 'up_call_expert_limit');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%user_params}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
