<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m200806_144838_add_settings_for_enable_original_phone_widget
 */
class m200806_144838_add_settings_for_enable_original_phone_widget extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'enable_original_phone_widget',
            's_name' => 'Enable Original Phone Widget',
            's_type' => Setting::TYPE_BOOL,
            's_value' => true,
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
            'enable_original_phone_widget'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
