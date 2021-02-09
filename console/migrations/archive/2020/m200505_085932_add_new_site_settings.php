<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m200505_085932_add_new_site_settings
 */
class m200505_085932_add_new_site_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'create_lead_on_incoming_call',
            's_name' => 'Create Lead on Incoming Call',
            's_type' => Setting::TYPE_BOOL,
            's_value' => 0,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_updated_user_id' => 1,
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'create_case_on_incoming_call',
            's_name' => 'Create Case on Incoming Call',
            's_type' => Setting::TYPE_BOOL,
            's_value' => 0,
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
            'create_case_on_incoming_call', 'create_lead_on_incoming_call'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
