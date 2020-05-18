<?php

use yii\db\Migration;

/**
 * Class m191004_123828_remove_column_l_grade_tbl_leads
 */
class m191004_123828_remove_column_l_grade_tbl_leads extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%leads}}', 'l_grade');
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
        $this->addColumn('{{%leads}}', 'l_grade', $this->tinyInteger()->defaultValue(0));
        Yii::$app->db->getSchema()->refreshTableSchema('{{%leads}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
