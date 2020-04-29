<?php

use yii\db\Migration;

/**
 * Class m200310_084541_add_setting_trash_cases_active_days_limit
 */
class m200310_084541_add_setting_trash_cases_active_days_limit extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'trash_cases_active_days_limit',
            's_name' => 'Trash Cases active days limit',
            's_type' => \common\models\Setting::TYPE_INT,
            's_value' => 0,
            's_updated_dt' => date('Y-m-d H:i:s'),
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
            'trash_cases_active_days_limit',
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
