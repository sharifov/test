<?php

use yii\db\Migration;

/**
 * Class m211022_111714_alter_table_data_of_payment_method
 */
class m211022_111714_alter_table_data_of_payment_method extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new \yii\db\Query())->createCommand($this->db)->setSql('update payment_method set pm_key = pm_short_name where pm_key is null')->execute();

        if ($paymentMethodCreditCard = \common\models\PaymentMethod::findOne(['pm_id' => 1])) {
            $paymentMethodCreditCard->pm_key = 'card';
            $paymentMethodCreditCard->save();
        }

        $this->alterColumn('{{%payment_method}}', 'pm_key', $this->string(50)->notNull()->unique());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%payment_method}}', 'pm_key', $this->string(50));
    }
}
