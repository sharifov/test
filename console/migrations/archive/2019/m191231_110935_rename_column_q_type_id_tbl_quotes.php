<?php

use yii\db\Migration;

/**
 * Class m191231_110935_rename_column_q_type_id_tbl_quotes
 */
class m191231_110935_rename_column_q_type_id_tbl_quotes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('{{%quotes}}', 'q_type_id', 'type_id');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%quotes}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('{{%quotes}}', 'type_id', 'q_type_id');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%quotes}}');
    }
}
