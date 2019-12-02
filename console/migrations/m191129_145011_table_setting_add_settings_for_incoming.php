<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m191129_145011_table_setting_add_settings_for_incoming
 */
class m191129_145011_table_setting_add_settings_for_incoming extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'create_new_lead_sms',
            's_name' => 'Create New Lead on SMS',
            's_type' => Setting::TYPE_BOOL,
            's_value' => false,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_updated_user_id' => 1,
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'create_new_support_case_sms',
            's_name' => 'Create New Support Case on SMS',
            's_type' => Setting::TYPE_BOOL,
            's_value' => false,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_updated_user_id' => 1,
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'create_new_exchange_case_sms',
            's_name' => 'Create New Exchange Case on SMS',
            's_type' => Setting::TYPE_BOOL,
            's_value' => false,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_updated_user_id' => 1,
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'create_new_lead_email',
            's_name' => 'Create New Lead on Email',
            's_type' => Setting::TYPE_BOOL,
            's_value' => false,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_updated_user_id' => 1,
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'create_new_exchange_case_email',
            's_name' => 'Create New Exchange Case on Email',
            's_type' => Setting::TYPE_BOOL,
            's_value' => false,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_updated_user_id' => 1,
        ]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'create_new_lead_sms', 'create_new_support_case_sms', 'create_new_exchange_case_sms',
            'create_new_lead_email', 'create_new_exchange_case_email'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
