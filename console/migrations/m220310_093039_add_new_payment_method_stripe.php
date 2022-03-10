<?php

use yii\db\Migration;

/**
 * Class m220310_093039_add_new_payment_method_stripe
 */
class m220310_093039_add_new_payment_method_stripe extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand()->upsert('{{%payment_method}}', [
            'pm_name' => 'Merchant Stripe (Client Tokenization)',
            'pm_key' => 'stripe',
            'pm_short_name' => 'STRIPE',
            'pm_enabled' => true
        ])->execute();
    }


    public function safeDown()
    {
        $this->delete('{{%payment_method}}', ['pm_key' => 'stripe']);
    }
}
