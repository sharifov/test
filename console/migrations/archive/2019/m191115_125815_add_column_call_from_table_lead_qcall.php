<?php

use yii\db\Migration;

/**
 * Class m191115_125815_add_column_call_from_table_lead_qcall
 */
class m191115_125815_add_column_call_from_table_lead_qcall extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%lead_qcall}}', 'lqc_call_from', $this->string(30));

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
        $this->dropColumn('{{%lead_qcall}}', 'lqc_call_from');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%lead_qcall}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
