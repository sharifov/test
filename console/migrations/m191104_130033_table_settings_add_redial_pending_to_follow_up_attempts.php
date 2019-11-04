<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m191104_130033_table_settings_add_redial_pending_to_follow_up_attempts
 */
class m191104_130033_table_settings_add_redial_pending_to_follow_up_attempts extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'redial_pending_to_follow_up_attempts',
            's_name' => 'Redial Pending to Follow Up attempts',
            's_type' => Setting::TYPE_INT,
            's_value' => 10,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_updated_user_id' => 1,
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
            'redial_pending_to_follow_up_attempts'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }


}
