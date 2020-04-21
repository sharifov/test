<?php

use yii\db\Migration;

/**
 * Class m191118_142729_add_column_reservation_time_table_lead_qcall
 */
class m191118_142729_add_column_reservation_time_table_lead_qcall extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%lead_qcall}}', 'lqc_reservation_time', $this->dateTime());

        Yii::$app->db->getSchema()->refreshTableSchema('{{%lead_qcall}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%lead_qcall}}', 'lqc_reservation_time');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%lead_qcall}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
