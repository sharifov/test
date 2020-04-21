<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m191118_142120_table_setting_add_redial_reservation_time
 */
class m191118_142120_table_setting_add_redial_reservation_time extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'redial_reservation_time',
            's_name' => 'Redial reservation time',
            's_type' => Setting::TYPE_INT,
            's_value' => 15,
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
            'redial_reservation_time'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }

}
