<?php

use yii\db\Migration;

/**
 * Class m201230_095556_add_column_l_status_dt_tbl_leads
 */
class m201230_095556_add_column_l_status_dt_tbl_leads extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%leads}}', 'l_status_dt', $this->dateTime());
        $this->createIndex('IND-leads_l_status_dt', '{{%leads}}', ['l_status_dt']);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-leads_l_status_dt', '{{%leads}}');
        $this->dropColumn('{{%leads}}', 'l_status_dt');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
