<?php

use yii\db\Migration;

/**
 * Class m191115_130339_add_column_phone_switch_table_qcall_config
 */
class m191115_130339_add_column_phone_switch_table_qcall_config extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%qcall_config}}', 'qc_phone_switch', $this->boolean()->defaultValue(false));

        Yii::$app->db->getSchema()->refreshTableSchema('{{%qcall_config}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%qcall_config}}', 'qc_phone_switch');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%qcall_config}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
