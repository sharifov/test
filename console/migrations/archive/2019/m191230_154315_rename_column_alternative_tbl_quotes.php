<?php

use yii\db\Migration;

/**
 * Class m191230_154315_rename_column_alternative_tbl_quotes
 */
class m191230_154315_rename_column_alternative_tbl_quotes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('{{%quotes}}', 'alternative', 'q_type_id');

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
        $this->renameColumn('{{%quotes}}', 'q_type_id', 'alternative');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%quotes}}');
    }
}
