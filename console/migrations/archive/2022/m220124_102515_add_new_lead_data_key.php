<?php

use yii\db\Migration;

/**
 * Class m220124_102515_add_new_lead_data_key
 */
class m220124_102515_add_new_lead_data_key extends Migration
{
    public function safeUp()
    {
        Yii::$app->db->createCommand()->upsert('{{%lead_data_key}}', [
            'ldk_key' => 'wp_exit_popup',
            'ldk_name' => 'Wordpress - Exit Pop-Up Parameter (Promo)',
            'ldk_enable' => true,
            'ldk_is_system' => true,
            'ldk_created_dt' => date('Y-m-d H:i:s')
        ])->execute();
    }


    public function safeDown()
    {
        $this->delete('{{%lead_data_key}}', ['IN', 'ldk_key', [
            'wp_exit_popup'
        ]]);
    }
}
