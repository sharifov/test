<?php

use yii\db\Migration;

/**
 * Class m191031_111403_table_settings_add_qcall_count_last_dialed_leads
 */
class m191031_111403_table_settings_add_qcall_count_last_dialed_leads extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'qcall_count_last_dialed_leads',
            's_name' => 'Qcall count last dialed leads hours',
            's_type' => 'int',
            's_value' => 5,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_updated_user_id' => 1,
        ]);
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'qcall_count_last_dialed_leads'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

}
