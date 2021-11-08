<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m211108_151525_add_abac_policy_for_product_quote_refund_obj
 */
class m211108_151525_add_abac_policy_for_product_quote_refund_obj extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->delete('{{%abac_policy}}', ['IN', 'ap_object', [
            'product-quote/product-quote/act/refund_quote/details',
        ]]);

        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '("admin" in r.sub.env.user.roles)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true}',
            'ap_object' => 'product/product-quote-refund/obj/product-quote-refund',
            'ap_action' => '(accessDetails)',
            'ap_action_json' => "[\"accessDetails\"]",
            'ap_effect' => 1,
            'ap_title' => '',
            'ap_sort_order' => 50,
            'ap_enabled' => 1,
            'ap_created_dt' => date('Y-m-d H:i:s'),
        ]);
        \Yii::$app->abac->invalidatePolicyCache();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%abac_policy}}', ['IN', 'ap_object', [
            'product/product-quote-refund/obj/product-quote-refund',
        ]]);
    }
}
