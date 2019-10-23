<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%quote_segment}}`.
 */
class m191023_061002_add_recheck_baggage_column_to_quote_segment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%quote_segment}}', 'qs_recheck_baggage', $this->boolean());

        Yii::$app->db->getSchema()->refreshTableSchema('{{%quote_segment}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%quote_segment}}', 'qs_recheck_baggage');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%quote_segment}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
