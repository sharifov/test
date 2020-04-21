<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%leads}}`.
 */
class m200207_101448_add_hybrid_uid_column_to_leads_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%leads}}', 'hybrid_uid', $this->string(15));

        Yii::$app->db->getSchema()->refreshTableSchema('{{%leads}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%leads}}', 'hybrid_uid');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%leads}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
