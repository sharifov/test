<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m191226_153951_table_settings_add_settings_for_api_user_rate
 */
class m191226_153951_table_settings_add_settings_for_api_user_rate extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'api_user_rate_settings',
            's_name' => 'Api user rate settings',
            's_type' => Setting::TYPE_ARRAY,
            's_value' => json_encode([
                'number' =>  1000000,
                'reset' =>  60,
            ]),
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
            'api_user_rate_settings'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
