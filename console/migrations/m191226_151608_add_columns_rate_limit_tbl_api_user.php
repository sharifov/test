<?php

use yii\db\Migration;

/**
 * Class m191226_151608_add_columns_rate_limit_tbl_api_user
 */
class m191226_151608_add_columns_rate_limit_tbl_api_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%api_user}}', 'au_rate_limit_number', $this->integer());
        $this->addColumn('{{%api_user}}', 'au_rate_limit_reset', $this->integer());

        Yii::$app->db->getSchema()->refreshTableSchema('{{%api_user}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%api_user}}', 'au_rate_limit_number');
        $this->dropColumn('{{%api_user}}', 'au_rate_limit_reset');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%api_user}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
