<?php

use yii\db\Migration;

/**
 * Class m211221_055417_add_ldk_we_first_call_not_picked
 */
class m211221_055417_add_ldk_we_first_call_not_picked extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand()->upsert('{{%lead_data_key}}', [
            'ldk_key' => 'we_first_call_not_picked',
            'ldk_name' => 'Is Web Engage First Call Not Picked',
            'ldk_enable' => true,
            'ldk_is_system' => true,
            'ldk_created_dt' => date('Y-m-d H:i:s')
        ])->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%lead_data_key}}', ['IN', 'ldk_key', [
            'we_first_call_not_picked'
        ]]);
    }
}
