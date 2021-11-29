<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m211125_163854_add_abac_rule_to_product_quote_show_status_log
 */
class m211125_163854_add_abac_rule_to_product_quote_show_status_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%abac_policy}}', [
            'ap_rule_type' => 'p',
            'ap_subject' => '(r.sub.env.available == true)',
            'ap_subject_json' => '{"condition":"AND","rules":[{"id":"env_available","field":"env.available","type":"boolean","input":"radio","operator":"==","value":true}],"valid":true}',
            'ap_object' => 'product/product-quote/obj/product-quote',
            'ap_action' => '(showStatusLog)',
            'ap_action_json' => "[\"showStatusLog\"]",
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
        $this->delete('{{%abac_policy}}', ['AND', ['IN', 'ap_object', [
            'product/product-quote/obj/product-quote',
        ]], ['IN', 'ap_action', ['(showStatusLog)']]]);

        \Yii::$app->abac->invalidatePolicyCache();
    }
}
