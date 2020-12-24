<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m201224_110524_remove_old_widget_settings
 */
class m201224_110524_remove_old_widget_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'enable_original_phone_widget'
        ]]);

        $this->delete('{{%setting}}', ['IN', 's_key', [
            'use_new_web_phone_widget'
        ]]);

        $this->delete('{{%setting}}', ['IN', 's_key', [
            'lead_communication_new_call_widget'
        ]]);

        $this->delete('{{%setting}}', ['IN', 's_key', [
            'case_communication_new_call_widget'
        ]]);

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
        $this->insert('{{%setting}}', [
            's_key' => 'enable_original_phone_widget',
            's_name' => 'Enable Original Phone Widget',
            's_type' => Setting::TYPE_BOOL,
            's_value' => false,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_updated_user_id' => 1,
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'use_new_web_phone_widget',
            's_name' => 'New WebPhone Widget',
            's_type' => \common\models\Setting::TYPE_BOOL,
            's_value' => 0,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_updated_user_id' => 1,
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'lead_communication_new_call_widget',
            's_name' => 'Lead communication block - init call in new phone widget',
            's_type' => \common\models\Setting::TYPE_BOOL,
            's_value' => 0,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => null
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'case_communication_new_call_widget',
            's_name' => 'Case communication block - init call in new phone widget',
            's_type' => \common\models\Setting::TYPE_BOOL,
            's_value' => 0,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => null
        ]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
