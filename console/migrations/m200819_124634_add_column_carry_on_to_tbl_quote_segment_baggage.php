<?php

use yii\db\Migration;

/**
 * Class m200819_124634_add_column_carry_on_to_tbl_quote_segment_baggage
 */
class m200819_124634_add_column_carry_on_to_tbl_quote_segment_baggage extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%quote_segment_baggage}}', 'qsb_carry_one', $this->boolean()->defaultValue(true));
        $this->refresh();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%quote_segment_baggage}}', 'qsb_carry_one');
        $this->refresh();
    }

    private function refresh()
    {
        Yii::$app->db->getSchema()->refreshTableSchema('{{%quote_segment_baggage}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
