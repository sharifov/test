<?php

use yii\db\Migration;

/**
 * Class m210914_140610_add_new_lead_data_key
 */
class m210914_140610_add_new_lead_data_key extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand()->upsert('{{%lead_data_key}}', [
            'ldk_key' => 'cross_system_xp',
            'ldk_name' => 'Cross-System Experiment ID',
            'ldk_enable' => true,
            'ldk_is_system' => false,
            'ldk_created_dt' => date('Y-m-d H:i:s')
        ])->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%lead_data_key}}', ['IN', 'ldk_key', [
            'cross_system_xp'
        ]]);
    }
}
