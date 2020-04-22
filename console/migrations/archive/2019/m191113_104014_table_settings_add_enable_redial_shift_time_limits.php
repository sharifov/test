<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m191113_104014_table_settings_add_enable_redial_shift_time_limits
 */
class m191113_104014_table_settings_add_enable_redial_shift_time_limits extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'enable_redial_shift_time_limits',
            's_name' => 'Enable Redial Shift Time limits',
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
            'enable_redial_shift_time_limits'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

}
