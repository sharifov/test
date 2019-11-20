<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m191120_072416_table_settings_add_redial_same_deadline_priority
 */
class m191120_072416_table_settings_add_redial_same_deadline_priority extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'redial_same_deadline_priority',
            's_name' => 'Redial same deadline priority',
            's_type' => Setting::TYPE_INT,
            's_value' => 60,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_updated_user_id' => 1,
        ]);

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');

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
            'redial_same_deadline_priority'
        ]]);

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
