<?php

use yii\db\Migration;

/**
 * Class m220103_122632_add_new_site_setting_for_manage_auth_via_auth_clients
 */
class m220103_122632_add_new_site_setting_for_manage_auth_via_auth_clients extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'enable_auth_clients',
            's_name' => 'Enable authorization via social media clients (Google)',
            's_type' => \common\models\Setting::TYPE_BOOL,
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
            'enable_auth_clients',
        ]]);
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
