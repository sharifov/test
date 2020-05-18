<?php

use yii\db\Migration;

/**
 * Class m200219_112157_add_settings_redial_business_flight_leads_skill_priority_time
 */
class m200219_112157_add_settings_redial_business_flight_leads_skill_priority_time extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'redial_business_flight_leads_skill_priority_time',
            's_name' => 'Redial Business Flight Leads skill priority time (minutes)',
            's_type' => \common\models\Setting::TYPE_INT,
            's_value' => 0,
            's_updated_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'redial_business_flight_leads_minimum_skill_level',
            's_name' => 'Redial Business Flight Leads minimum skill level',
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
            'redial_business_flight_leads_skill_priority_time',
            'redial_business_flight_leads_minimum_skill_level',
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
