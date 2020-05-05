<?php

use yii\db\Migration;

/**
 * Class m200317_183706_add_site_setting_api_create_lead_flight_product
 */
class m200317_183706_add_site_setting_api_create_lead_flight_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'api_create_lead_flight_product',
            's_name' => 'Api create lead flight product',
            's_type' => \common\models\Setting::TYPE_BOOL,
            's_value' => false,
            's_updated_dt' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'api_create_lead_flight_product',
        ]]);
    }
}
