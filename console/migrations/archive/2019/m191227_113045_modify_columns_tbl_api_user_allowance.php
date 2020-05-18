<?php

use yii\db\Migration;

/**
 * Class m191227_113045_modify_columns_tbl_api_user_allowance
 */
class m191227_113045_modify_columns_tbl_api_user_allowance extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%api_user_allowance}}', 'aua_allowed_number_requests', $this->bigInteger()->notNull());
        $this->alterColumn('{{%api_user_allowance}}', 'aua_last_check_time', $this->bigInteger()->notNull());

        Yii::$app->db->getSchema()->refreshTableSchema('{{%api_user_allowance}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%api_user_allowance}}', 'aua_allowed_number_requests', $this->integer(10)->notNull());
        $this->alterColumn('{{%api_user_allowance}}', 'aua_last_check_time', $this->integer(10)->notNull());

        Yii::$app->db->getSchema()->refreshTableSchema('{{%api_user_allowance}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
