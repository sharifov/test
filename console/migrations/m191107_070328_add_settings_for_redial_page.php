<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m191107_070328_add_settings_for_redial_page
 */
class m191107_070328_add_settings_for_redial_page extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'enable_redial_show_lead_limit',
            's_name' => 'Enable Redial show lead limit',
            's_type' => Setting::TYPE_BOOL,
            's_value' => false,
            's_updated_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'count_as_taken_leads_created_manually',
            's_name' => 'Count as Taken leads created manually',
            's_type' => Setting::TYPE_BOOL,
            's_value' => false,
            's_updated_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'count_as_taken_leads_from_incoming_call',
            's_name' => 'Count as Taken leads from incoming call',
            's_type' => Setting::TYPE_BOOL,
            's_value' => false,
            's_updated_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'enable_redial_default_take_limit',
            's_name' => 'Enable Redial Default take limit',
            's_type' => Setting::TYPE_BOOL,
            's_value' => false,
            's_updated_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'enable_min_percent_for_take_leads',
            's_name' => 'Enable Min percent for take leads',
            's_type' => Setting::TYPE_BOOL,
            's_value' => false,
            's_updated_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'enable_take_frequency_minutes',
            's_name' => 'Enable Take Frequency Minutes',
            's_type' => Setting::TYPE_BOOL,
            's_value' => false,
            's_updated_dt' => date('Y-m-d H:i:s'),
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
            'enable_redial_show_lead_limit', 'count_as_taken_leads_created_manually', 'count_as_taken_leads_from_incoming_call',
            'enable_redial_default_take_limit', 'enable_min_percent_for_take_leads', 'enable_take_frequency_minutes',
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

}
