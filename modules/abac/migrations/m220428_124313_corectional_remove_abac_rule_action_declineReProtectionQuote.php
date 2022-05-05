<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m220428_124313_corectional_remove_abac_rule_action_declineReProtectionQuote
 */
class m220428_124313_corectional_remove_abac_rule_action_declineReProtectionQuote extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->delete('{{%abac_policy}}', ['AND', ['IN', 'ap_object', [
            'product/product-quote/obj/product-quote',
        ]], ['IN', 'ap_action', ['(declineReProtectionQuote)']]]);

        \Yii::$app->abac->invalidatePolicyCache();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220428_124313_corectional_remove_abac_rule_action_declineReProtectionQuote cannot be reverted.\n";
    }
}
