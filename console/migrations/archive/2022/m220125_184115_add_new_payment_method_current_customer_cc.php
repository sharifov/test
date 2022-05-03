<?php

use yii\db\Migration;

/**
 * Class m220124_102515_add_new_lead_data_key
 */
class m220125_184115_add_new_payment_method_current_customer_cc extends Migration
{
    public function safeUp()
    {
        Yii::$app->db->createCommand()->upsert('{{%payment_method}}', [
            'pm_name' => 'Current Credit Card',
            'pm_key' => 'current_card',
            'pm_short_name' => 'CURRENT_CC',
            'pm_enabled' => true
        ])->execute();
    }


    public function safeDown()
    {
        $this->delete('{{%payment_method}}', ['pm_key' => 'current_card']);
    }
}
