<?php

use yii\db\Migration;

/**
 * Class m220202_094902_add_new_site_setting_snooze_limit
 */
class m220202_094902_add_new_site_setting_snooze_limit extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'snooze_limit',
            's_name' => 'Snooze limit',
            's_type' => \common\models\Setting::TYPE_INT,
            's_value' => 10,
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
            'snooze_limit',
        ]]);
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
