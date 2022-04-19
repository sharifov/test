<?php

use yii\db\Migration;

/**
 * Class m211206_064538_add_ldk_we_email_replied
 */
class m211206_064538_add_ldk_we_email_replied extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand()->upsert('{{%lead_data_key}}', [
            'ldk_key' => 'we_email_replied',
            'ldk_name' => 'We email replied',
            'ldk_enable' => false,
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
            'we_email_replied'
        ]]);
    }
}
