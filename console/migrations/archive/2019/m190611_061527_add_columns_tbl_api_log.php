<?php

use yii\db\Migration;

/**
 * Class m190611_061527_add_columns_tbl_api_log
 */
class m190611_061527_add_columns_tbl_api_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%api_log}}', 'al_execution_time', $this->decimal(6, 3));
        $this->addColumn('{{%api_log}}', 'al_memory_usage', $this->integer());

        $this->addColumn('{{%api_log}}', 'al_db_execution_time', $this->decimal(6, 3));
        $this->addColumn('{{%api_log}}', 'al_db_query_count', $this->integer());


        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%api_log}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%api_log}}', 'al_execution_time');
        $this->dropColumn('{{%api_log}}', 'al_memory_usage');
        $this->dropColumn('{{%api_log}}', 'al_db_execution_time');
        $this->dropColumn('{{%api_log}}', 'al_db_query_count');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%api_log}}');
    }


}
