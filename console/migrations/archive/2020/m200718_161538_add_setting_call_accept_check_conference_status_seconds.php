<?php

use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m200718_161538_add_setting_call_accept_check_conference_status_seconds
 */
class m200718_161538_add_setting_call_accept_check_conference_status_seconds extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'call_accept_check_conference_status_seconds',
            's_name' => 'Call accept check conference status seconds',
            's_type' => \common\models\Setting::TYPE_INT,
            's_value' => 60,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => null,
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
            'call_accept_check_conference_status_seconds'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
