<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m191125_135655_table_setting_add_redial_max_attempts_for_dates_passed
 */
class m191125_135655_table_setting_add_redial_max_attempts_for_dates_passed extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'redial_max_attempts_for_dates_passed',
            's_name' => 'Redial max attempts for dates passed',
            's_type' => Setting::TYPE_INT,
            's_value' => 20,
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
            'redial_max_attempts_for_dates_passed'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
