<?php

use yii\db\Migration;

/**
 * Class m190805_122620_add_column_tbl_user_project_params
 */
class m190805_122620_add_column_tbl_user_project_params extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_project_params}}', 'upp_allow_general_line', $this->boolean()->defaultValue(true));
        Yii::$app->db->getSchema()->refreshTableSchema('{{%user_project_params}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_project_params}}', 'upp_allow_general_line');
        Yii::$app->db->getSchema()->refreshTableSchema('{{%user_project_params}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

}
