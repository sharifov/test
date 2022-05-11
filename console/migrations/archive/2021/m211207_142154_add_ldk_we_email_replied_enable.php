<?php

use yii\db\Migration;

/**
 * Class m211207_142154_add_ldk_we_email_replied_enable
 */
class m211207_142154_add_ldk_we_email_replied_enable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand()->upsert('{{%lead_data_key}}', [
            'ldk_key' => 'we_email_replied',
            'ldk_name' => 'We email replied',
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
        Yii::$app->db->createCommand()->upsert('{{%lead_data_key}}', [
            'ldk_key' => 'we_email_replied',
            'ldk_name' => 'We email replied',
            'ldk_enable' => false,
            'ldk_is_system' => true,
            'ldk_created_dt' => date('Y-m-d H:i:s')
        ])->execute();
    }
}
